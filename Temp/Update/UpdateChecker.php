<?php

/*
 * This file is part of the Update Checker.
 *
 * (c) 2014 Stephan Wentz
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Temp\Update;

class UpdateChecker
{
    /**
     * Checks a composer.lock file.
     *
     * @param string $lock   The path to the composer.lock file
     *
     * @return array Result array
     *
     * @throws \InvalidArgumentException When the output format is unsupported
     * @throws \RuntimeException         When the lock file does not exist
     * @throws \RuntimeException         When curl does not work or is unavailable
     */
    public function check($lock)
    {
        if (!function_exists('curl_init')) {
            throw new \RuntimeException('Curl is required to use this command.');
        }

        if (is_dir($lock) && file_exists($lock.'/composer.lock')) {
            $lock = $lock.'/composer.lock';
        } elseif (preg_match('/composer\.json$/', $lock)) {
            $lock = str_replace('composer.json', 'composer.lock', $lock);
        }

        if (!is_file($lock)) {
            throw new \RuntimeException('Lock file does not exist.');
        }

        $results = array();

        $data = json_decode(file_get_contents($lock), 1);
        foreach ($data['packages'] as $package) {
            if (false === $curl = curl_init()) {
                throw new \RuntimeException('Unable to create a new curl handle.');
            }

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HEADER, true);
            curl_setopt($curl, CURLOPT_URL, 'https://packagist.org/packages/'.$package['name'].'.json');
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($curl, CURLOPT_TIMEOUT, 10);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curl, CURLOPT_MAXREDIRS, 3);
            curl_setopt($curl, CURLOPT_FAILONERROR, false);

            $response = curl_exec($curl);

            if (false === $response) {
                $error = curl_error($curl);
                curl_close($curl);

                throw new \RuntimeException(sprintf('An error occurred: %s.', $error));
            }

            $headersSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
            $body = substr($response, $headersSize);

            $name = $package['name'];
            $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if (200 != $statusCode) {
                curl_close($curl);

                $results[$name] = array('name' => $name, 'error' => true, 'message' => 'not found');
                continue;
            }

            curl_close($curl);

            $version = $package['version'];
            $remoteData = json_decode($body, true);
            $latestStableVersion = 0;
            foreach ($remoteData['package']['versions'] as $versionName => $versionData) {
                $remoteVersion = $versionData['version'];
                $normalizedVersion = $versionData['version_normalized'];
                if (!$this->isStable($normalizedVersion)) {
                    continue;
                }
                if (version_compare($latestStableVersion, $normalizedVersion, '<')) {
                    $latestStableVersion = $normalizedVersion;
                }
                if ($remoteVersion === $version) {
                    $version = $normalizedVersion;
                }
            }
            if ($latestStableVersion && version_compare($version, $latestStableVersion, '<')) {
                $results[$name] = array('name' => $name, 'local' => $version, 'remote' => $latestStableVersion);
            }
        }

        return $results;
    }

    /**
     * @param string $version
     *
     * @return boolean
     */
    private function isStable($version)
    {
        return strpos($version, 'dev') === false && strpos($version, 'alpha') === false && strpos($version, 'beta') === false;
    }
}

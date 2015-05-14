<?php

/*
 * This file is part of the Update Checker.
 *
 * (c) 2014 Stephan Wentz
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Temp\Update\Formatters;

use Symfony\Component\Console\Output\OutputInterface;

class JsonFormatter implements FormatterInterface
{
    /**
     * Displays a security report as json.
     *
     * @param OutputInterface $output
     * @param string          $lockFilePath    The file path to the checked lock file
     * @param array           $packages An array of packages
     */
    public function displayResults(OutputInterface $output, $lockFilePath, array $packages)
    {
        if (defined('JSON_PRETTY_PRINT')) {
            $output->write(json_encode($packages, JSON_PRETTY_PRINT));
        } else {
            $output->write(json_encode($packages));
        }
    }
}

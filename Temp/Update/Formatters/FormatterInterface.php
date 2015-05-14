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

interface FormatterInterface
{
    /**
     * Displays a security report as json.
     *
     * @param OutputInterface $output
     * @param string          $lockFilePath    The file path to the checked lock file
     * @param array           $data            An array of packaeges
     */
    public function displayResults(OutputInterface $output, $lockFilePath, array $data);
}

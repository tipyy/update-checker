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
use Symfony\Component\Console\Helper\TableHelper;

class TextFormatter implements FormatterInterface
{
    public function __construct(TableHelper $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * Displays a security report as plain text.
     *
     * @param OutputInterface $output
     * @param string          $lockFilePath    The file path to the checked lock file
     * @param array           $data            An array of packaeges
     */
    public function displayResults(OutputInterface $output, $lockFilePath, array $data)
    {
        $this->formatter->setHeaders(array('Name', 'Locale', 'Remote'));
        $this->formatter->setPadType(STR_PAD_LEFT);
        $hasRows = false;
        foreach ($data as $name => $status) {
            if (empty($status['error'])) {
                $hasRows = true;
                $this->formatter->addRow($status);
            } elseif ($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
                $output->writeln('<error>' . $name . ' ' . $status['message'] . '</error>');
            }
        }
        if (!$hasRows) {
            $output->writeln('<info>No updated packages found.</info>');
        } else {
            $this->formatter->render($output);
        }
    }
}

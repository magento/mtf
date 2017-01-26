<?php
/**
 * Copyright © 2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Console;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console output of formatted messages.
 */
class Output
{
    /**
     * CLI output.
     *
     * @var OutputInterface
     */
    private $output;

    /**
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * Output info and error messages to console.
     *
     * @param array|null $messages
     * @return void
     */
    public function outputMessages(array $messages = null)
    {
        foreach ($messages as $type => $message) {
            $this->output->writeln("<$type>" . implode(PHP_EOL, $message) . "</$type>");
        }
    }

    /**
     * Writes a message to the output and adds a newline at the end.
     *
     * @param string $message
     * @return void
     */
    public function writeln($message)
    {
        $this->output->writeln($message);
    }
}

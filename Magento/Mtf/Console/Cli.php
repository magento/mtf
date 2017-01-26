<?php
/**
 * Copyright © 2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Console;

use Symfony\Component\Console\Application as SymfonyApplication;

/**
 * MTF CLI Application.
 */
class Cli extends SymfonyApplication
{
    /**
     * Command list collector.
     *
     * @var CommandListInterface
     */
    private $commandList;

    /**
     * @param CommandListInterface $commandList
     * @param string $name The name of the application
     * @param string $version The version of the application
     */
    public function __construct(CommandListInterface $commandList, $name = 'UNKNOWN', $version = 'UNKNOWN')
    {
        parent::__construct($name, $version);
        $this->commandList = $commandList;
        $this->registerAnalyzers();
    }

    /**
     * Register tool commands.
     *
     * @return void
     */
    private function registerAnalyzers()
    {
        foreach ($this->commandList->getCommands() as $command) {
            $this->add($command);
        }
    }
}

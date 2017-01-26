<?php
/**
 * Copyright © 2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Console;

/**
 * Class CommandList has a list of commands, which can be extended via DI configuration.
 */
class CommandList implements CommandListInterface
{
    /**
     * Commands array.
     *
     * @var \Symfony\Component\Console\Command\Command[]
     */
    private $commands;

    /**
     * @param array $commands [optional]
     */
    public function __construct(array $commands = [])
    {
        $this->commands = $commands;
    }

    /**
     * Gets list of command instances.
     *
     * @return \Symfony\Component\Console\Command\Command[]
     */
    public function getCommands()
    {
        return $this->commands;
    }
}

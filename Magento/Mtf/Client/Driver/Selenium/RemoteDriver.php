<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Client\Driver\Selenium;

use Magento\Mtf\Config\DataInterface;

/**
 * Class RemoteDriver
 * Entry point to selenium
 *
 * @api
 */
class RemoteDriver extends \PHPUnit_Extensions_Selenium2TestCase
{
    /**
     * Timeout for wait until
     *
     * @var int
     */
    protected $timeout;

    /**
     * Constructor
     *
     * @param DataInterface $config
     */
    public function __construct(DataInterface $config)
    {
        $this->timeout = $config->get('server/0/item/selenium/seleniumServerRequestsTimeout', 10) * 1000;
    }

    /**
     * Wait until callback isn't null or timeout occurs
     *
     * @param callable $callback
     * @param null|int $timeout
     * @return mixed
     */
    public function waitUntil($callback, $timeout = null)
    {
        $timeout = $this->timeout !== null ? $this->timeout : $timeout;
        return \Magento\Mtf\Client\Driver\Selenium\WaitUntil::run($callback, $this, $timeout);
    }

    /**
     * Force set new session strategy. This will lead to clear all previous data stored in session
     *
     * @param string $strategy
     * @return void
     */
    public function setSessionStrategy($strategy)
    {
        $this->setUpSessionStrategy(['sessionStrategy' => $strategy]);
    }

    /**
     * Avoid serialization of closure
     * 
     * @return array
     */
    public function __sleep()
    {
        return [];
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Client\Driver\Selenium;

/**
 * Class TestCase
 *
 * Entry point to selenium
 *
 * @package Mtf\Client\Driver\Selenium
 * @api
 */
class TestCase extends \PHPUnit_Extensions_Selenium2TestCase
{
    /**
     * Timeout for waitUntil
     * @var int
     */
    private $_timeout;

    /**
     * Constructor
     *
     * @constructor
     * @param \Mtf\System\Config $config
     */
    public function __construct(\Mtf\System\Config $config)
    {
        $this->_timeout = $config->getConfigParam('server/selenium/seleniumServerRequestsTimeout', 10) * 1000;
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
        $waitUntil = new WaitUntil($this);
        return $waitUntil->run($callback, $this->_timeout);
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
}

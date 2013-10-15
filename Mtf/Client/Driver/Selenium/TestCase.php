<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
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
     * @param int $timeout
     */
    public function __construct($timeout = 10000)
    {
        $this->_timeout = $timeout;
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
     */
    public function setSessionStrategy($strategy)
    {
        $this->setUpSessionStrategy(array('sessionStrategy' => $strategy));
    }
}

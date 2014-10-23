<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Client\Driver\Selenium;

use Mtf\System\Event\EventManager;
use Mtf\System\Config;

/**
 * Class TestCase
 * Entry point to selenium
 *
 * @api
 */
class TestCase extends \PHPUnit_Extensions_Selenium2TestCase
{
    /**
     * Event Manager instance
     *
     * @var EventManager
     */
    protected $eventManager;

    /**
     * Timeout for wait until
     *
     * @var int
     */
    protected $timeout;

    /**
     * Constructor
     *
     * @constructor
     * @param Config $config
     * @param EventManager $eventManager
     */
    public function __construct(Config $config, EventManager $eventManager)
    {
        $this->timeout = $config->getConfigParam('server/selenium/seleniumServerRequestsTimeout', 10) * 1000;
        $this->eventManager = $eventManager;
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
        $waitUntil = new WaitUntil(/*$this*/);
        return $waitUntil->run($callback, $this->timeout);
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

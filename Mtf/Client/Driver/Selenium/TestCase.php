<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Client\Driver\Selenium;

use Magento\Framework\Exception;
use Mtf\Client\Driver\Selenium\WaitUntil;
use Mtf\System\Event\EventManager;


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
     * @var EventManager
     */
    protected $eventManager;

    /**
     * @var WaitUntil
     */
    protected $waitUntil;

    /**
     * Constructor
     *
     * @constructor
     * @param WaitUntil $waitUntil
     * @param EventManager $eventManager
     */
    public function __construct(
        WaitUntil $waitUntil,
        EventManager $eventManager
    ) {
        $this->waitUntil = $waitUntil;
        $this->eventManager = $eventManager;
    }

    /**
     * Wait until callback isn't null or timeout occurs
     *
     * @param $callback
     * @param null $timeout
     */
    public function waitUntil($callback, $timeout = null)
    {
        $implicitWait = $this->timeouts()->getLastImplicitWaitValue();
        try {
            $this->timeouts()->implicitWait(0);
            $this->waitUntil->run($callback, $timeout);
            $this->timeouts()->implicitWait($implicitWait);
        } catch (\Exception $e) {
            $this->timeouts()->implicitWait($implicitWait);
        }
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

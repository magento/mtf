<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Client\Driver\Selenium;

use Mtf\Client\Driver\Selenium\TestCase;

/**
 * Class WaitUntil
 *
 * Implements waitUntil method functionality
 *
 * @package Mtf\Client\Driver\Selenium
 * @api
 */
class WaitUntil
{
    /**
     * PHPUnit Test Case instance
     *
     * @var TestCase
     */
    private $testCase;

    /**
     * Default timeout, ms
     *
     * @var int
     */
    private $defaultTimeout = 40000;

    /**
     * The sleep interval between iterations, ms
     *
     * @var int
     */
    private $defaultSleepInterval = 500;

    /**
     * @constructor
     * @param TestCase $testCase
     */
    public function __construct(TestCase $testCase)
    {
        $this->testCase = $testCase;
    }

    /**
     * Run timeout waiting script
     *
     * @param callback $callback Callback to run until it returns not null or timeout occurs
     * @param null|int $timeout
     * @return mixed
     * @throws \PHPUnit_Extensions_Selenium2TestCase_Exception
     * @throws \PHPUnit_Extensions_Selenium2TestCase_WebDriverException
     */
    public function run($callback, $timeout = null)
    {
        if (!is_callable($callback)) {
            throw new \PHPUnit_Extensions_Selenium2TestCase_Exception('The valid callback is expected');
        }

        // if there was an implicit timeout specified - remember it and temporarily turn it off
        $implicitWait = $this->testCase->timeouts()->getLastImplicitWaitValue();
        if ($implicitWait) {
            $this->testCase->timeouts()->implicitWait(0);
        }
        if (is_null($timeout)) {
            $timeout = $this->defaultTimeout;
        }
        $timeout /= 1000;
        $endTime = microtime(true) + $timeout;
        $lastException = null;
        while (true) {
            try {
                $result = call_user_func($callback);
                if (!is_null($result)) {
                    if ($implicitWait) {
                        $this->testCase->timeouts()->implicitWait($implicitWait);
                    }
                    return $result;
                }
            } catch (\Exception $e) {
                $lastException = $e;
            }
            if (microtime(true) > $endTime) {
                if ($implicitWait) {
                    $this->testCase->timeouts()->implicitWait($implicitWait);
                }
                $message = "Timed out after {$timeout} second" . ($timeout != 1 ? 's' : '');
                throw new \PHPUnit_Extensions_Selenium2TestCase_WebDriverException(
                    $message,
                    \PHPUnit_Extensions_Selenium2TestCase_WebDriverException::Timeout,
                    $lastException
                );
            }
            usleep($this->defaultSleepInterval * 1000);
        }
    }
}

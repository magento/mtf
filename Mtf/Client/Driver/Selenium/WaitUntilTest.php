<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Client\Driver\Selenium;

/**
 * Class WaitUntil
 * Implements waitUntil method functionality
 *
 * @api
 */
class WaitUntilTest
{

    /**
     * Default timeout, ms
     *
     * @var int
     */
    const DEFAULT_TIMEOUT = 40000;

    /**
     * The sleep interval between iterations, ms
     *
     * @var int
     */
    const DEFAULT_SLEEP_INTERVAL = 500;

    /**
     * Run timeout waiting script
     *
     * @param callback $callback Callback to run until it returns not null or timeout occurs
     * @param null|int $timeout
     * @return mixed
     * @throws \PHPUnit_Extensions_Selenium2TestCase_Exception
     * @throws \PHPUnit_Extensions_Selenium2TestCase_WebDriverException
     */
    public static function run($callback, $testCase, $timeout = null)
    {
        if (!is_callable($callback)) {
            throw new \PHPUnit_Extensions_Selenium2TestCase_Exception('The valid callback is expected');
        }

        // if there was an implicit timeout specified - remember it and temporarily turn it off
        $implicitWait = $testCase->timeouts()->getLastImplicitWaitValue();
        if ($implicitWait) {
            $testCase->timeouts()->implicitWait(0);
        }
        if (is_null($timeout)) {
            $timeout = self::DEFAULT_TIMEOUT;
        }
        $timeout /= 1000;
        $endTime = microtime(true) + $timeout;
        $lastException = null;
        while (true) {
            try {
                $result = call_user_func($callback);
                if (!is_null($result)) {
                    if ($implicitWait) {
                        $testCase->timeouts()->implicitWait($implicitWait);
                    }
                    return $result;
                }
            } catch (\Exception $e) {
                $lastException = $e;
            }
            if (microtime(true) > $endTime) {
                if ($implicitWait) {
                    $testCase->timeouts()->implicitWait($implicitWait);
                }
                $message = "Timed out after {$timeout} second" . ($timeout != 1 ? 's' : '');
                throw new \PHPUnit_Extensions_Selenium2TestCase_WebDriverException(
                    $message,
                    \PHPUnit_Extensions_Selenium2TestCase_WebDriverException::Timeout,
                    $lastException
                );
            }
            usleep(self::DEFAULT_SLEEP_INTERVAL * 1000);
        }
    }
}

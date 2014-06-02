<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Client\Driver\Selenium;

use Mtf\System\Config;

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
     * Request timeout config path
     */
    const REQUEST_TIMEOUT_CONFIG_PATH = 'server/selenium/seleniumServerRequestsTimeout';

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
     * Constructor
     *
     * @constructor
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->defaultTimeout = $config->getConfigParam(self::REQUEST_TIMEOUT_CONFIG_PATH, 10) * 1000;
    }

    /**
     * Run timeout waiting script
     *
     * @param callback $callback Callback to run until it returns not null or timeout occurs
     * @param null|int $timeout
     * @return void
     * @throws \PHPUnit_Extensions_Selenium2TestCase_Exception
     * @throws \PHPUnit_Extensions_Selenium2TestCase_WebDriverException
     */
    public function run($callback, $timeout = null)
    {
        if (!is_callable($callback)) {
            throw new \PHPUnit_Extensions_Selenium2TestCase_Exception('The valid callback is expected');
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
                    break;
                }
            } catch (\Exception $e) {
                $lastException = $e;
            }
            if (microtime(true) > $endTime) {
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

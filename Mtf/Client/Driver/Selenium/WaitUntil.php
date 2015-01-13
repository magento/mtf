<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mtf\Client\Driver\Selenium;

/**
 * Class WaitUntil
 * Implements waitUntil method functionality
 *
 * @api
 */
class WaitUntil
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
     * @param $testCase
     * @param null|int $timeout
     * @return mixed
     * @throws \PHPUnit_Extensions_Selenium2TestCase_Exception
     * @throws \PHPUnit_Extensions_Selenium2TestCase_WebDriverException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
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

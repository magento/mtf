<?php
/**
 * Copyright © 2017 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\System\Browser;

use Magento\Mtf\Factory\Factory;
use Magento\Mtf\Config\DataInterface;

/**
 * Class Listener.
 * This listener provides strategy of reopening browser according reopenBrowser config.
 *
 * @internal
 */
class Listener implements \PHPUnit\Framework\TestListener
{
    /**
     * Scope
     */
    const SCOPE_TEST = 'test';
    const SCOPE_TEST_CASE = 'testCase';

    /**
     * Current scope
     *
     * @var string
     */
    protected $_scope;

    /**
     * @param DataInterface $configuration
     */
    public function __construct(DataInterface $configuration = null)
    {
        if (!isset($configuration)) {
            $configuration = \Magento\Mtf\ObjectManagerFactory::getObjectManager()
                ->getInstance()
                ->get('Magento\Mtf\Config\GlobalConfig');
        }
        $this->_scope = $configuration->get('application/0/reopenBrowser/0/value') ? : static::SCOPE_TEST_CASE;
    }

    /**
     * An error occurred
     *
     * @param \PHPUnit\Framework\Test $test
     * @param \Exception $e
     * @param float $time
     * @return void
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addError(\PHPUnit\Framework\Test $test, \Exception $e, $time)
    {
        //
    }

    /**
     * @param \PHPUnit\Framework\Test $test
     * @param \PHPUnit\Framework\Warning $e
     * @param float $time
     */
    public function addWarning(\PHPUnit\Framework\Test $test, \PHPUnit\Framework\Warning $e, $time)
    {
        //
    }

    /**
     * A failure occurred
     *
     * @param \PHPUnit\Framework\Test $test
     * @param \PHPUnit\Framework\AssertionFailedError $e
     * @param float $time
     * @return void
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addFailure(\PHPUnit\Framework\Test $test, \PHPUnit\Framework\AssertionFailedError $e, $time)
    {
        //
    }

    /**
     * Incomplete test
     *
     * @param \PHPUnit\Framework\Test $test
     * @param \Exception $e
     * @param float $time
     * @return void
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addIncompleteTest(\PHPUnit\Framework\Test $test, \Exception $e, $time)
    {
        //
    }

    /**
     * Risky test
     *
     * @param \PHPUnit\Framework\Test $test
     * @param \Exception $e
     * @param float $time
     * @return void
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addRiskyTest(\PHPUnit\Framework\Test $test, \Exception $e, $time)
    {
        //
    }

    /**
     * Skipped test
     *
     * @param \PHPUnit\Framework\Test $test
     * @param \Exception $e
     * @param float $time
     * @return void
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addSkippedTest(\PHPUnit\Framework\Test $test, \Exception $e, $time)
    {
        //
    }

    /**
     * A test suite started.
     *
     * @param \PHPUnit\Framework\TestSuite $suite
     * @return void
     */
    public function startTestSuite(\PHPUnit\Framework\TestSuite $suite)
    {
        if (class_exists($suite->getName()) && is_subclass_of($suite->getName(), \PHPUnit\Framework\TestCase::class)) {
            $this->_run(static::SCOPE_TEST_CASE);
        }
    }

    /**
     * A test suite ended
     *
     * @param \PHPUnit\Framework\TestSuite $suite
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function endTestSuite(\PHPUnit\Framework\TestSuite $suite)
    {
        //
    }

    /**
     * A test started
     *
     * @param \PHPUnit\Framework\Test $test
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function startTest(\PHPUnit\Framework\Test $test)
    {
        if ($this->_isBrowserFailed()) {
            $this->_reopenBrowser();
        } else {
            $this->_run(static::SCOPE_TEST);
        }
    }

    /**
     * A test ended
     *
     * @param \PHPUnit\Framework\Test $test
     * @param float $time
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function endTest(\PHPUnit\Framework\Test $test, $time)
    {
        //
    }

    /**
     * Reopen browser for current scope.
     * Reopening is skipped for first time.
     *
     * @param string $scope
     * @return void
     */
    protected function _run($scope)
    {
        if ($scope != $this->_scope) {
            return;
        }
        static $runCounter = 0;
        if (0 < $runCounter++) {
            $this->_reopenBrowser();
        }
    }

    /**
     * Validate if browser was terminated
     *
     * @return bool
     */
    protected function _isBrowserFailed()
    {
        try {
            $browser = Factory::getClientBrowser();
            //If browser was terminated every browser method call will throw specific exception
            $browser->getUrl();
        } catch (\PHPUnit_Extensions_Selenium2TestCase_WebDriverException $e) {
            return true;
        }
        return false;
    }

    /**
     * Force reopen browser
     * @return void
     */
    protected function _reopenBrowser()
    {
        Factory::getClientBrowser()->reopen();
    }
}

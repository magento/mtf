<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\System\Browser;

use Exception;
use Magento\Mtf\Factory\Factory;
use Magento\Mtf\Config\DataInterface;
use PHPUnit_Framework_AssertionFailedError;
use PHPUnit_Framework_Test;
use PHPUnit_Framework_TestSuite;

/**
 * Class Listener.
 * This listener provides strategy of reopening browser according reopenBrowser config.
 *
 * @internal
 */
class Listener implements \PHPUnit_Framework_TestListener
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
     * @param PHPUnit_Framework_Test $test
     * @param Exception $e
     * @param float $time
     * @return void
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addError(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        //
    }

    /**
     * A failure occurred
     *
     * @param PHPUnit_Framework_Test $test
     * @param PHPUnit_Framework_AssertionFailedError $e
     * @param float $time
     * @return void
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        //
    }

    /**
     * Incomplete test
     *
     * @param PHPUnit_Framework_Test $test
     * @param Exception $e
     * @param float $time
     * @return void
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        //
    }

    /**
     * Risky test
     *
     * @param PHPUnit_Framework_Test $test
     * @param Exception $e
     * @param float $time
     * @return void
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addRiskyTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        //
    }

    /**
     * Skipped test
     *
     * @param PHPUnit_Framework_Test $test
     * @param Exception $e
     * @param float $time
     * @return void
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        //
    }

    /**
     * A test suite started.
     *
     * @param PHPUnit_Framework_TestSuite $suite
     * @return void
     */
    public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        if (class_exists($suite->getName()) && is_subclass_of($suite->getName(), '\\PHPUnit_Framework_TestCase')) {
            $this->_run(static::SCOPE_TEST_CASE);
        }
    }

    /**
     * A test suite ended
     *
     * @param PHPUnit_Framework_TestSuite $suite
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        //
    }

    /**
     * A test started
     *
     * @param PHPUnit_Framework_Test $test
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function startTest(PHPUnit_Framework_Test $test)
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
     * @param PHPUnit_Framework_Test $test
     * @param float $time
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function endTest(PHPUnit_Framework_Test $test, $time)
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

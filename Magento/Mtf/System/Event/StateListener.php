<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\System\Event;

/**
 * Class StateListener
 */
class StateListener implements \PHPUnit_Framework_TestListener
{

    /**
     * An error occurred.
     *
     * @param \PHPUnit_Framework_Test $test
     * @param \Exception $e
     * @param float $time
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addError(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        //
    }

    /**
     * A failure occurred
     *
     * @param \PHPUnit_Framework_Test $test
     * @param \PHPUnit_Framework_AssertionFailedError $e
     * @param float $time
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addFailure(\PHPUnit_Framework_Test $test, \PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        //
    }

    /**
     * Incomplete test
     *
     * @param \PHPUnit_Framework_Test $test
     * @param \Exception $e
     * @param float $time
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addIncompleteTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        //
    }

    /**
     * Risky test
     *
     * @param \PHPUnit_Framework_Test $test
     * @param \Exception $e
     * @param float $time
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addRiskyTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        //
    }

    /**
     * Skipped test
     *
     * @param \PHPUnit_Framework_Test $test
     * @param \Exception $e
     * @param float $time
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addSkippedTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        //
    }

    /**
     * A test suite started
     *
     * @param \PHPUnit_Framework_TestSuite $suite
     * @return void
     */
    public function startTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        if ('default' === State::getTestSuiteName()) {
            State::setTestSuiteName($suite->getName());
        }
    }

    /**
     * A test suite ended
     *
     * @param \PHPUnit_Framework_TestSuite $suite
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function endTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        State::setTestSuiteName('default');
    }

    /**
     * Listener for test starting
     *
     * @param \PHPUnit_Framework_Test $test
     * @return void
     */
    public function startTest(\PHPUnit_Framework_Test $test)
    {
        State::setTestClassName(get_class($test));
        State::setTestMethodName($test->getName());
    }

    /**
     * A test ended
     *
     * @param \PHPUnit_Framework_Test $test
     * @param float $time
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function endTest(\PHPUnit_Framework_Test $test, $time)
    {
        //
    }
}

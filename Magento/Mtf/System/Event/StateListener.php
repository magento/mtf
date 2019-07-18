<?php
/**
 * Copyright © 2017 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\System\Event;

/**
 * Class StateListener
 */
class StateListener implements \PHPUnit\Framework\TestListener
{

    /**
     * An error occurred.
     *
     * @param \PHPUnit\Framework\Test $test
     * @param \Exception $e
     * @param float $time
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addError(\PHPUnit\Framework\Test $test, \Throwable $e, float $time): void
    {
        //
    }

    /**
     * @param \PHPUnit\Framework\Test $test
     * @param \PHPUnit\Framework\Warning $e
     * @param float $time
     */
    public function addWarning(\PHPUnit\Framework\Test $test, \PHPUnit\Framework\Warning $e, float $time): void
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
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addFailure(\PHPUnit\Framework\Test $test, \PHPUnit\Framework\AssertionFailedError $e, float $time): void
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
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addIncompleteTest(\PHPUnit\Framework\Test $test, \Throwable $e, float $time): void
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
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addRiskyTest(\PHPUnit\Framework\Test $test, \Throwable $e, float $time): void
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
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addSkippedTest(\PHPUnit\Framework\Test $test, \Throwable $e, float $time): void
    {
        //
    }

    /**
     * A test suite started
     *
     * @param \PHPUnit\Framework\TestSuite $suite
     * @return void
     */
    public function startTestSuite(\PHPUnit\Framework\TestSuite $suite): void
    {
        if ('default' === State::getTestSuiteName()) {
            State::setTestSuiteName($suite->getName());
        }
    }

    /**
     * A test suite ended
     *
     * @param \PHPUnit\Framework\TestSuite $suite
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function endTestSuite(\PHPUnit\Framework\TestSuite $suite): void
    {
        State::setTestSuiteName('default');
    }

    /**
     * Listener for test starting
     *
     * @param \PHPUnit\Framework\Test $test
     * @return void
     */
    public function startTest(\PHPUnit\Framework\Test $test): void
    {
        State::setTestClassName(get_class($test));
        State::setTestMethodName($test->getName());
    }

    /**
     * A test ended
     *
     * @param \PHPUnit\Framework\Test $test
     * @param float $time
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function endTest(\PHPUnit\Framework\Test $test, float $time): void
    {
        //
    }
}

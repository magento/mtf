<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Mtf\System\Event;

use Mtf\System\Event\State;

/**
 * Class StateListener
 * @package Mtf\System\Event
 */
class StateListener implements \PHPUnit_Framework_TestListener
{

    public function addError(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        //
    }

    public function addFailure(\PHPUnit_Framework_Test $test, \PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        //
    }

    public function addIncompleteTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        //
    }

    public function addSkippedTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        //
    }

    public function startTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        State::setTestSuiteName($suite->getName());
    }

    public function endTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        //
    }

    /**
     * Listener for test starting
     *
     * @param \PHPUnit_Framework_Test $test
     */
    public function startTest(\PHPUnit_Framework_Test $test)
    {
        State::setTestClassName(get_class($test));
        State::setTestMethodName($test->getName());
    }

    public function endTest(\PHPUnit_Framework_Test $test, $time)
    {
        //
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Mtf\System\Event;

use Mtf\System\Event\State;

class StateListener implements \PHPUnit_Framework_TestListener
{
    /**
     * @var \Mtf\System\Event\State
     */
    protected $stateObject;

    public function __construct(State $stateObject)
    {
        $this->stateObject = $stateObject;
    }

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
        $this->stateObject->setTestSuiteName($suite->getName());
    }

    public function endTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        //
    }

    public function startTest(\PHPUnit_Framework_Test $test)
    {
        $this->stateObject->setTestMethodName($test->getName());
    }

    public function endTest(\PHPUnit_Framework_Test $test, $time)
    {
        //
    }
}

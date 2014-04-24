<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\TestRunner\Process;

/**
 * Class ProcessTestResult
 *
 * Cleans up the TestResult returned from PHPUnit in cases where the result
 * cannot be serialized entirely (due to the use of closures or other
 * non-serializable data).
 */
class TestResult extends \PHPUnit_Framework_TestResult
{

    /**
     * Adds an error to the list of errors.
     *
     * @param  \PHPUnit_Framework_Test $test
     * @param  \Exception $e
     * @param  float $time
     */
    public function addError(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        if ($e instanceof \PHPUnit_Framework_IncompleteTest) {
            $this->notImplemented[] = new \PHPUnit_Framework_TestFailure(
                $test, $e
            );

            $notifyMethod = 'addIncompleteTest';

            if ($this->stopOnIncomplete) {
                $this->stop();
            }
        } else {
            if ($e instanceof \PHPUnit_Framework_SkippedTest) {
                $this->skipped[] = new \PHPUnit_Framework_TestFailure($test, $e);
                $notifyMethod = 'addSkippedTest';

                if ($this->stopOnSkipped) {
                    $this->stop();
                }
            } else {
                if ($e instanceof \PHPUnit_Extensions_Selenium2TestCase_Exception) {
                    $e = new TestResultException($e);
                }
                $this->errors[] = new \PHPUnit_Framework_TestFailure($test, $e);
                $notifyMethod = 'addError';

                if ($this->stopOnError || $this->stopOnFailure) {
                    $this->stop();
                }
            }
        }

        foreach ($this->listeners as $listener) {
            $listener->$notifyMethod($test, $e, $time);
        }

        $this->lastTestFailed = true;
        $this->time += $time;
    }
}

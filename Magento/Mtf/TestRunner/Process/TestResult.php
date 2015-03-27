<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\TestRunner\Process;

use Magento\Mtf\TestRunner\Process\Exception\Failure;
use Magento\Mtf\TestRunner\Process\Exception\Skipped;
use Magento\Mtf\TestRunner\Process\Exception\Risky;
use Magento\Mtf\TestRunner\Process\Exception\Incomplete;
use Magento\Mtf\TestCase\Injectable;

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
     * @param \PHPUnit_Framework_Test $test
     * @param \Exception $e
     * @param float $time
     * @return void
     */
    public function addError(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        $e = $this->wrapException($e);
        $variation = 0;
        if ($test instanceof Injectable) {
            $variation = $test->getVariationName();
        }

        if ($e instanceof \PHPUnit_Framework_RiskyTest) {
            $this->risky[$variation] = new \PHPUnit_Framework_TestFailure($test, $e);

            $notifyMethod = 'addRiskyTest';

            if ($this->stopOnRisky) {
                $this->stop();
            }
        } elseif ($e instanceof \PHPUnit_Framework_IncompleteTest) {
            $this->notImplemented[$variation] = new \PHPUnit_Framework_TestFailure($test, $e);

            $notifyMethod = 'addIncompleteTest';

            if ($this->stopOnIncomplete) {
                $this->stop();
            }
        } elseif ($e instanceof \PHPUnit_Framework_SkippedTest) {
            $this->skipped[$variation] = new \PHPUnit_Framework_TestFailure($test, $e);
            $notifyMethod = 'addSkippedTest';

            if ($this->stopOnSkipped) {
                $this->stop();
            }
        } else {
            $this->errors[$variation] = new \PHPUnit_Framework_TestFailure($test, $e);
            $notifyMethod = 'addError';

            if ($this->stopOnError || $this->stopOnFailure) {
                $this->stop();
            }
        }

        foreach ($this->listeners as $listener) {
            $listener->$notifyMethod($test, $e, $time);
        }

        $this->lastTestFailed = true;
        $this->time += $time;
    }

    /**
     * Adds a failure to the list of failures.
     * The passed in exception caused the failure.
     *
     * @param \PHPUnit_Framework_Test $test
     * @param \PHPUnit_Framework_AssertionFailedError $e
     * @param float $time
     * @return void
     */
    public function addFailure(\PHPUnit_Framework_Test $test, \PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        $e = $this->wrapException($e);
        $variation = null;
        if ($test instanceof Injectable) {
            $variation = $test->getVariationName();
        }

        if ($e instanceof \PHPUnit_Framework_RiskyTest) {
            $this->risky[$variation] = new \PHPUnit_Framework_TestFailure($test, $e);

            $notifyMethod = 'addRiskyTest';

            if ($this->stopOnRisky) {
                $this->stop();
            }
        } elseif ($e instanceof \PHPUnit_Framework_IncompleteTest) {
            $this->notImplemented[$variation] = new \PHPUnit_Framework_TestFailure($test, $e);

            $notifyMethod = 'addIncompleteTest';

            if ($this->stopOnIncomplete) {
                $this->stop();
            }
        } elseif ($e instanceof \PHPUnit_Framework_SkippedTest) {
            $this->skipped[$variation] = new \PHPUnit_Framework_TestFailure($test, $e);
            $notifyMethod = 'addSkippedTest';

            if ($this->stopOnSkipped) {
                $this->stop();
            }
        } else {
            $this->failures[$variation] = new \PHPUnit_Framework_TestFailure($test, $e);
            $notifyMethod = 'addFailure';

            if ($this->stopOnFailure) {
                $this->stop();
            }
        }

        foreach ($this->listeners as $listener) {
            $listener->$notifyMethod($test, $e, $time);
        }

        $this->lastTestFailed = true;
        $this->time += $time;
    }

    /**
     * @param \Exception $exception
     * @return Failure|Incomplete|Risky|Skipped
     */
    protected function wrapException(\Exception $exception)
    {
        switch (true) {
            case ($exception instanceof \PHPUnit_Framework_RiskyTest):
                $wrappedException = new Risky($exception);
                break;
            case ($exception instanceof \PHPUnit_Framework_IncompleteTest):
                $wrappedException = new Incomplete($exception);
                break;
            case ($exception instanceof \PHPUnit_Framework_SkippedTest):
                $wrappedException = new Skipped($exception);
                break;
            default:
                $wrappedException = new Failure($exception);
        }
        return $wrappedException;
    }

    /**
     * Serialize only required information
     *
     * @return array
     */
    public function __sleep()
    {
        return ['time', 'notImplemented', 'risky', 'skipped', 'errors', 'failures', 'codeCoverage'];
    }
}

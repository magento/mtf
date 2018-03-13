<?php
/**
 * Copyright © 2017 Magento. All rights reserved.
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
class TestResult extends \PHPUnit\Framework\TestResult
{
    /**
     * Adds an error to the list of errors.
     *
     * @param \PHPUnit\Framework\Test $test
     * @param \Exception $e
     * @param float $time
     * @return void
     */
    public function addError(\PHPUnit\Framework\Test $test, $e, $time)
    {
        $e = $this->wrapException($e);
        $variation = 0;
        if ($test instanceof Injectable) {
            $variation = $test->getVariationName();
        }

        if ($e instanceof \PHPUnit\Framework\RiskyTest) {
            $this->risky[$variation] = new \PHPUnit\Framework\TestFailure($test, $e);

            $notifyMethod = 'addRiskyTest';

            if ($this->stopOnRisky) {
                $this->stop();
            }
        } elseif ($e instanceof \PHPUnit\Framework\IncompleteTest) {
            $this->notImplemented[$variation] = new \PHPUnit\Framework\TestFailure($test, $e);

            $notifyMethod = 'addIncompleteTest';

            if ($this->stopOnIncomplete) {
                $this->stop();
            }
        } elseif ($e instanceof \PHPUnit\Framework\SkippedTest) {
            $this->skipped[$variation] = new \PHPUnit\Framework\TestFailure($test, $e);
            $notifyMethod = 'addSkippedTest';

            if ($this->stopOnSkipped) {
                $this->stop();
            }
        } else {
            $this->errors[$variation] = new \PHPUnit\Framework\TestFailure($test, $e);
            $notifyMethod = 'addError';

            if ($this->stopOnError || $this->stopOnFailure) {
                $this->stop();
            }
        }

        if ($e instanceof \Error) {
            $e = new \PHPUnit\Framework\ExceptionWrapper($e);
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
     * @param \PHPUnit\Framework\Test $test
     * @param \PHPUnit\Framework\AssertionFailedError $e
     * @param float $time
     * @return void
     */
    public function addFailure(\PHPUnit\Framework\Test $test, \PHPUnit\Framework\AssertionFailedError $e, $time)
    {
        $e = $this->wrapException($e);
        $variation = null;
        if ($test instanceof Injectable) {
            $variation = $test->getVariationName();
        }

        if ($e instanceof \PHPUnit\Framework\RiskyTest || $e instanceof \PHPUnit\Framework\OutputError) {
            $this->risky[$variation] = new \PHPUnit\Framework\TestFailure($test, $e);

            $notifyMethod = 'addRiskyTest';

            if ($this->stopOnRisky) {
                $this->stop();
            }
        } elseif ($e instanceof \PHPUnit\Framework\IncompleteTest) {
            $this->notImplemented[$variation] = new \PHPUnit\Framework\TestFailure($test, $e);

            $notifyMethod = 'addIncompleteTest';

            if ($this->stopOnIncomplete) {
                $this->stop();
            }
        } elseif ($e instanceof \PHPUnit\Framework\SkippedTest) {
            $this->skipped[$variation] = new \PHPUnit\Framework\TestFailure($test, $e);
            $notifyMethod = 'addSkippedTest';

            if ($this->stopOnSkipped) {
                $this->stop();
            }
        } else {
            $this->failures[$variation] = new \PHPUnit\Framework\TestFailure($test, $e);
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
     * Wrap an exception.
     * 
     * @param \Exception $exception
     * @return Failure|Incomplete|Risky|Skipped
     */
    protected function wrapException(\Exception $exception)
    {
        switch (true) {
            case ($exception instanceof \PHPUnit\Framework\RiskyTest):
                $wrappedException = new Risky($exception);
                break;
            case ($exception instanceof \PHPUnit\Framework\IncompleteTest):
                $wrappedException = new Incomplete($exception);
                break;
            case ($exception instanceof \PHPUnit\Framework\SkippedTest):
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

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

namespace Mtf\TestRunner\Process;

use Mtf\TestRunner\Process\Exception\Failure;
use Mtf\TestRunner\Process\Exception\Skipped;
use Mtf\TestRunner\Process\Exception\Risky;
use Mtf\TestRunner\Process\Exception\Incomplete;

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
        parent::addError($test, $this->wrapException($e), $time);
    }

    /**
     * Adds a failure to the list of failures.
     * The passed in exception caused the failure.
     *
     * @param \PHPUnit_Framework_Test                 $test
     * @param \PHPUnit_Framework_AssertionFailedError $e
     * @param float                                  $time
     */
    public function addFailure(\PHPUnit_Framework_Test $test, \PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        parent::addFailure($test, $this->wrapException($e), $time);
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

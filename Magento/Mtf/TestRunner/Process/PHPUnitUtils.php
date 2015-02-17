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
namespace Magento\Mtf\TestRunner\Process;

/**
 * Class Process
 */
class PHPUnitUtils extends \PHPUnit_Util_PHP
{
    /**
     * @param string $job
     * @param array $settings
     * @return array|null|void
     * @throws \PHPUnit_Framework_Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function runJob($job, array $settings = [])
    {
        throw new \PHPUnit_Framework_Exception('Should not call this method.');
    }

    /**
     * Override the protected method to not do anything when processing a pipe.
     *
     * @param resource $pipe
     * @param string $job
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function process($pipe, $job)
    {
        //
    }

    /**
     * Makes the processChildResult inherited method public.
     *
     * @param \PHPUnit_Framework_Test $test
     * @param \PHPUnit_Framework_TestResult $result
     * @param string $stdout
     * @param string $stderr
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function processChildResult(
        \PHPUnit_Framework_Test $test,
        \PHPUnit_Framework_TestResult $result,
        $stdout,
        $stderr
    ) {
        $time = 0;

        if (!empty($stderr)) {
            $result->addError($test, new \PHPUnit_Framework_Exception(trim($stderr)), $time);
        } else {
            set_error_handler(
                function ($errno, $errstr, $errfile, $errline) {
                    throw new \ErrorException($errstr, $errno, $errno, $errfile, $errline);
                }
            );
            try {
                if (strpos($stdout, "#!/usr/bin/env php\n") === 0) {
                    $stdout = substr($stdout, 19);
                }

                $childResult = unserialize(str_replace("#!/usr/bin/env php\n", '', $stdout));
                restore_error_handler();
            } catch (\ErrorException $e) {
                restore_error_handler();
                $childResult = false;

                $result->addError($test, new \PHPUnit_Framework_Exception(trim($stdout), 0, $e), $time);
            }

            if ($childResult !== false) {
                if (!empty($childResult['output'])) {
                    print $childResult['output'];
                }

                $test->setResult($childResult['testResult']);
                $test->addToAssertionCount($childResult['numAssertions']);

                $childResult = $childResult['result'];

                if ($result->getCollectCodeCoverageInformation()) {
                    $result->getCodeCoverage()->merge($childResult->getCodeCoverage());
                }

                $time = $childResult->time();
                $notImplemented = $childResult->notImplemented();
                $risky = $childResult->risky();
                $skipped = $childResult->skipped();
                $errors = $childResult->errors();
                $failures = $childResult->failures();

                if (!empty($notImplemented)) {
                    foreach ($notImplemented as $variation => $error) {
                        $name = $test->getName();
                        $test->setName($name . " variation $variation");
                        $result->addError($test, $this->getException($error), $time);
                        $test->setName($name);
                    }
                }
                if (!empty($risky)) {
                    foreach ($risky as $variation => $error) {
                        $name = $test->getName();
                        $test->setName($name . " variation $variation");
                        $result->addError($test, $this->getException($error), $time);
                        $test->setName($name);
                    }
                }
                if (!empty($skipped)) {
                    foreach ($skipped as $variation => $error) {
                        $name = $test->getName();
                        $test->setName($name . " variation $variation");
                        $result->addError($test, $this->getException($error), $time);
                        $test->setName($name);
                    }
                }
                if (!empty($errors)) {
                    foreach ($errors as $variation => $error) {
                        $name = $test->getName();
                        $test->setName($name . " variation $variation");
                        $result->addError($test, $this->getException($error), $time);
                        $test->setName($name);
                    }
                }
                if (!empty($failures)) {
                    foreach ($failures as $variation => $failure) {
                        $name = $test->getName();
                        $test->setName($name . " variation $variation");
                        $result->addFailure($test, $this->getException($failure), $time);
                        $test->setName($name);
                    }
                }
            }
        }

        $result->endTest($test, $time);
    }

    /**
     * Gets the thrown exception from a PHPUnit_Framework_TestFailure.
     *
     * @param  \PHPUnit_Framework_TestFailure $error
     * @return \Exception
     * @since  Method available since Release 3.6.0
     * @see    https://github.com/sebastianbergmann/phpunit/issues/74
     */
    public function getException(\PHPUnit_Framework_TestFailure $error)
    {
        $exception = $error->thrownException();

        if ($exception instanceof \__PHP_Incomplete_Class) {
            $exceptionArray = array();
            foreach ((array)$exception as $key => $value) {
                $key = substr($key, strrpos($key, "\0") + 1);
                $exceptionArray[$key] = $value;
            }

            $exception = new \PHPUnit_Framework_SyntheticError(
                sprintf('%s: %s', $exceptionArray['_PHP_Incomplete_Class_Name'], $exceptionArray['message']),
                $exceptionArray['code'],
                $exceptionArray['file'],
                $exceptionArray['line'],
                $exceptionArray['trace']
            );
        }

        return $exception;
    }
}

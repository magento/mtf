<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\TestSuite;

use Magento\Mtf\TestRunner\Process\ProcessManager;

/**
 * Class TestSuite
 *
 * @api
 */
class TestSuite extends \PHPUnit_Framework_TestSuite
{
    /**
     * Overload the run to allow the process manager to run the testsuites.
     *
     * @param  \PHPUnit_Framework_TestResult $result
     * @param  false|bool $filter
     * @param  array $groups
     * @param  array $excludeGroups
     * @param  false|bool $processIsolation
     *
     * @return \PHPUnit_Framework_TestResult
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function run(
        \PHPUnit_Framework_TestResult $result = null,
        $filter = false,
        array $groups = [],
        array $excludeGroups = [],
        $processIsolation = false
    ) {
        if ($result === null) {
            $result = $this->createResult();
        }

        $classShortName = (new \ReflectionClass($this))->getShortName();
        if ('InjectableTestCase' == $classShortName && !$this->validate()) {
            return $result;
        }

        $result->startTestSuite($this);

        //@TODO setUpBeforeClass and tearDownAfterClass are not supported for parallel run
        $doSetup = false;

        if (!empty($excludeGroups)) {
            foreach ($this->groups as $_group => $_tests) {
                if (in_array($_group, $excludeGroups) && count($_tests) == count($this->tests)) {
                    $doSetup = false;
                }
            }
        }

        if ($doSetup) {
            try {
                $this->setUp();

                if ($this->testCase
                    // Some extensions use test names that are not classes;
                    // The method_exists() triggers an autoload call that causes issues with die()ing autoloaders.
                    && class_exists($this->name, false)
                    && method_exists($this->name, 'setUpBeforeClass')
                ) {
                    call_user_func([$this->name, 'setUpBeforeClass']);
                }
            } catch (\PHPUnit_Framework_SkippedTestSuiteError $e) {
                $numTests = count($this);
                for ($i = 0; $i < $numTests; $i++) {
                    $result->addFailure($this, $e, 0);
                }

                return $result;
            } catch (\Exception $e) {
                $numTests = count($this);
                for ($i = 0; $i < $numTests; $i++) {
                    $result->addError($this, $e, 0);
                }

                return $result;
            }
        }

        if (empty($groups)) {
            $tests = $this->tests;
        } else {
            $tests = new \SplObjectStorage;

            foreach ($groups as $group) {
                if (isset($this->groups[$group])) {
                    foreach ($this->groups[$group] as $test) {
                        $tests->attach($test);
                    }
                }
            }
        }

        foreach ($tests as $test) {
            if ($result->shouldStop()) {
                break;
            }

            if ($test instanceof \PHPUnit_Framework_TestSuite) {
                $test->setBackupGlobals($this->backupGlobals);
                $test->setBackupStaticAttributes($this->backupStaticAttributes);

                $test->run($result, $filter, $groups, $excludeGroups, $processIsolation);
            } else {
                $runTest = true;

                if ($filter !== false) {
                    $tmp = \PHPUnit_Util_Test::describe($test, false);

                    if ($tmp[0] != '') {
                        $name = join('::', $tmp);
                    } else {
                        $name = $tmp[1];
                    }

                    if (preg_match($filter, $name) == 0) {
                        $runTest = false;
                    }
                }

                if ($runTest && !empty($excludeGroups)) {
                    foreach ($this->groups as $_group => $_tests) {
                        if (in_array($_group, $excludeGroups)) {
                            foreach ($_tests as $_test) {
                                if ($test === $_test) {
                                    $runTest = false;
                                    break 2;
                                }
                            }
                        }
                    }
                }

                if ($runTest) {
                    if ($test instanceof \PHPUnit_Framework_TestCase) {
                        $test->setBackupGlobals($this->backupGlobals);
                        $test->setBackupStaticAttributes($this->backupStaticAttributes);
                        $test->setRunTestInSeparateProcess($processIsolation);
                    }

                    $this->runTest($test, $result);
                }
            }
        }

        /* Unique lines for TestSuite */
        $this->waitForProcessesToComplete();

        if ($doSetup) {
            if ($this->testCase
                // Some extensions use test names that are not classes;
                // The method_exists() triggers an autoload call that causes issues with die()ing autoloaders.
                && class_exists($this->name, false)
                && method_exists($this->name, 'tearDownAfterClass')
            ) {
                call_user_func([$this->name, 'tearDownAfterClass']);
            }

            $this->tearDown();
        }

        $result->endTestSuite($this);

        return $result;
    }

    /**
     * Wait for parallel processes to complete (for parallel run)
     * @return void
     */
    protected function waitForProcessesToComplete()
    {
        /** @var ProcessManager $processManager */
        $processManager = ProcessManager::factory();
        $processManager->waitForProcessesToComplete();
    }

    /**
     * Check whether a test method is public
     *
     * @param \ReflectionMethod $method
     * @return boolean
     */
    public static function isPublicTestMethod(\ReflectionMethod $method)
    {
        return (strpos($method->name, 'test') === 0 && $method->isPublic());
    }
}

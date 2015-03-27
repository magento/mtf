<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\TestCase;

/**
 * A TestCase defines the fixture to run multiple tests.
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Enable or disable the backup and restoration of the $GLOBALS array.
     * Overwrite this attribute in a child class of TestCase.
     * Setting this attribute in setUp() has no effect!
     *
     * @var boolean
     */
    protected $backupGlobals = null;

    /**
     * @var array
     */
    protected $backupGlobalsBlacklist = [];

    /**
     * Enable or disable the backup and restoration of static attributes.
     * Overwrite this attribute in a child class of TestCase.
     * Setting this attribute in setUp() has no effect!
     *
     * @var boolean
     */
    protected $backupStaticAttributes = null;

    /**
     * @var array
     */
    protected $backupStaticAttributesBlacklist = [];

    /**
     * Whether or not this test is to be run in a separate PHP process.
     *
     * @var boolean
     */
    protected $runTestInSeparateProcess = null;

    /**
     * Whether or not this test is running in a separate PHP process.
     *
     * @var boolean
     */
    private $inIsolation = false;

    /**
     * @var array
     */
    private $iniSettings = [];

    /**
     * @var array
     */
    private $locale = [];

    /**
     * @var array
     */
    private $mockObjects = [];

    /**
     * @var integer
     */
    private $status;

    /**
     * @var string
     */
    private $statusMessage = '';

    /**
     * @var integer
     */
    private $numAssertions = 0;

    /**
     * @var mixed
     */
    private $testResult;

    /**
     * @var string
     */
    private $output = '';

    /**
     * @var string
     */
    private $outputExpectedRegex = null;

    /**
     * @var string
     */
    private $outputExpectedString = null;

    /**
     * @var bool
     */
    private $hasPerformedExpectationsOnOutput = false;

    /**
     * @var mixed
     */
    private $outputCallback = false;

    /**
     * @var boolean
     */
    private $outputBufferingActive = false;

    /**
     * Runs the bare test sequence.
     *
     * @return void
     */
    public function runBare()
    {
        $this->numAssertions = 0;

        // Backup the $GLOBALS array and static attributes.
        if ($this->runTestInSeparateProcess !== true && $this->inIsolation !== true) {
            if ($this->backupGlobals === null || $this->backupGlobals === true) {
                \PHPUnit_Util_GlobalState::backupGlobals(
                    $this->backupGlobalsBlacklist
                );
            }

            if ($this->backupStaticAttributes === true) {
                \PHPUnit_Util_GlobalState::backupStaticAttributes(
                    $this->backupStaticAttributesBlacklist
                );
            }
        }

        // Start output buffering.
        ob_start();
        $this->outputBufferingActive = true;

        // Clean up stat cache.
        clearstatcache();

        // Backup the cwd
        $currentWorkingDirectory = getcwd();

        $hookMethods = \PHPUnit_Util_Test::getHookMethods(get_class($this));

        try {
            $this->checkRequirements();

            if ($this->inIsolation) {
                foreach ($hookMethods['beforeClass'] as $method) {
                    $this->$method();
                }
            }

            $this->setExpectedExceptionFromAnnotation();

            foreach ($hookMethods['before'] as $method) {
                $this->$method();
            }

            $this->assertPreConditions();
            $this->testResult = $this->runTest();
            $this->verifyMockObjects();
            $this->assertPostConditions();
            $this->status = \PHPUnit_Runner_BaseTestRunner::STATUS_PASSED;
        } catch (\PHPUnit_Framework_IncompleteTest $e) {
            $this->status = \PHPUnit_Runner_BaseTestRunner::STATUS_INCOMPLETE;
            $this->statusMessage = $e->getMessage();
        } catch (\PHPUnit_Framework_SkippedTest $e) {
            $this->status = \PHPUnit_Runner_BaseTestRunner::STATUS_SKIPPED;
            $this->statusMessage = $e->getMessage();
        } catch (\PHPUnit_Framework_AssertionFailedError $e) {
            $this->status = \PHPUnit_Runner_BaseTestRunner::STATUS_FAILURE;
            $this->statusMessage = $e->getMessage();
            $this->eventManager->dispatchEvent(['failure'], [$this->statusMessage]);
        } catch (\Exception $e) {
            $this->status = \PHPUnit_Runner_BaseTestRunner::STATUS_ERROR;
            $this->statusMessage = $e->getMessage();
        }

        // Clean up the mock objects.
        $this->mockObjects = [];

        // Tear down the fixture. An exception raised in tearDown() will be
        // caught and passed on when no exception was raised before.
        try {
            foreach ($hookMethods['after'] as $method) {
                $this->$method();
            }

            if ($this->inIsolation) {
                foreach ($hookMethods['afterClass'] as $method) {
                    $this->$method();
                }
            }
        } catch (\Exception $_e) {
            if (!isset($e)) {
                $e = $_e;
            }
        }

        // Stop output buffering.
        if ($this->outputCallback === false) {
            $this->output = ob_get_contents();
        } else {
            $this->output = call_user_func_array(
                $this->outputCallback, [ob_get_contents()]
            );
        }

        ob_end_clean();
        $this->outputBufferingActive = false;

        // Clean up stat cache.
        clearstatcache();

        // Restore the cwd if it was changed by the test
        if ($currentWorkingDirectory != getcwd()) {
            chdir($currentWorkingDirectory);
        }

        // Restore the $GLOBALS array and static attributes.
        if ($this->runTestInSeparateProcess !== true && $this->inIsolation !== true) {
            if ($this->backupGlobals === null || $this->backupGlobals === true) {
                \PHPUnit_Util_GlobalState::restoreGlobals(
                    $this->backupGlobalsBlacklist
                );
            }

            if ($this->backupStaticAttributes === true) {
                \PHPUnit_Util_GlobalState::restoreStaticAttributes();
            }
        }

        // Clean up INI settings.
        foreach ($this->iniSettings as $varName => $oldValue) {
            ini_set($varName, $oldValue);
        }

        $this->iniSettings = [];

        // Clean up locale settings.
        foreach ($this->locale as $category => $locale) {
            setlocale($category, $locale);
        }

        // Perform assertion on output.
        if (!isset($e)) {
            try {
                if ($this->outputExpectedRegex !== null) {
                    $this->hasPerformedExpectationsOnOutput = true;
                    $this->assertRegExp($this->outputExpectedRegex, $this->output);
                    $this->outputExpectedRegex = null;
                } elseif ($this->outputExpectedString !== null) {
                    $this->hasPerformedExpectationsOnOutput = true;
                    $this->assertEquals($this->outputExpectedString, $this->output);
                    $this->outputExpectedString = null;
                }
            } catch (\Exception $_e) {
                $e = $_e;
            }
        }

        // Workaround for missing "finally".
        if (isset($e)) {
            $this->onNotSuccessfulTest($e);
        }
    }
}

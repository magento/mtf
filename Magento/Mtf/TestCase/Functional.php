<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\TestCase;

use Magento\Mtf\TestRunner\Process\ProcessManager;

/**
 * Class Functional
 *
 * Class is extended from PHPUnit_Framework_TestCase
 * Used for test cases based on old specification
 * "Injectable" abstract Class should be used instead
 *
 * @api
 * @abstract
 */
abstract class Functional extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Mtf\ObjectManager
     */
    protected $objectManager;

    /**
     * @var bool
     */
    protected $isParallelRun = false;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var string
     */
    protected $dataName = '';

    /**
     * The name of the test suite.
     *
     * @var    string
     */
    protected $name = '';

    /**
     * The instance of the process manager.
     *
     * @var ProcessManager
     */
    protected $processManager;

    /**
     * @var \Magento\Mtf\System\Event\EventManagerInterface
     */
    protected $eventManager;

    /**
     * Whether or not this test is running in a separate PHP process.
     *
     * @var boolean
     */
    protected $inIsolation = false;

    /**
     * @var array
     */
    protected $iniSettings = [];

    /**
     * @var array
     */
    protected $locale = [];

    /**
     * @var integer
     */
    protected $status;

    /**
     * @var string
     */
    protected $statusMessage = '';

    /**
     * @var integer
     */
    protected $numAssertions = 0;

    /**
     * @var string
     */
    protected $output = '';

    /**
     * @var string
     */
    protected $outputExpectedRegex = null;

    /**
     * @var string
     */
    protected $outputExpectedString = null;

    /**
     * @var bool
     */
    protected $hasPerformedExpectationsOnOutput = false;

    /**
     * @var mixed
     */
    protected $outputCallback = false;

    /**
     * @var boolean
     */
    protected $outputBufferingActive = false;

    /**
     * Is test incomplete.
     *
     * @var bool
     */
    protected $isIncomplete = false;

    /**
     * @var array
     */
    protected $mockObjects = [];

    /**
     * Constructs a test case with the given name.
     *
     * @param null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        $this->name = $name;
        $this->data = $data;
        $this->dataName = $dataName;

        /** @var ProcessManager $processManager */
        $this->processManager = ProcessManager::factory();
        $this->setParallelRun($this->processManager->isParallelModeSupported());

        parent::__construct($name, $data, $dataName);

        $this->eventManager = $this->getObjectManager()->get('Magento\Mtf\System\Event\EventManagerInterface');

        $this->_construct();
    }

    /**
     * Get Object Manager instance
     *
     * @return \Magento\Mtf\ObjectManager
     */
    protected function getObjectManager()
    {
        if (!$this->objectManager) {
            $this->objectManager = \Magento\Mtf\ObjectManagerFactory::getObjectManager();
        }
        return $this->objectManager;
    }

    /**
     * Protected construct for child test cases
     *
     * @return void
     */
    protected function _construct()
    {
        //
    }

    /**
     * Run with Process Manager
     *
     * @param \PHPUnit_Framework_TestResult $result
     * @return \PHPUnit_Framework_TestResult
     * @throws \Exception
     */
    public function run(\PHPUnit_Framework_TestResult $result = null)
    {
        if ($this->isParallelRun) {
            $params = [
                'name' => $this->getName(false),
                'data' => $this->data,
                'dataName' => $this->dataName
            ];
            $this->processManager->run($this, $result, $params);
        } else {
            try {
                \PHP_Timer::start();
                parent::run($result);
            } catch (\PHPUnit_Framework_AssertionFailedError $phpUnitException) {
                $this->eventManager->dispatchEvent(['failure'], [$phpUnitException->getMessage()]);
                $result->addFailure($this, $phpUnitException, \PHP_Timer::stop());
            } catch (\Exception $exception) {
                $this->eventManager->dispatchEvent(['exception'], [$exception->getMessage()]);
                $result->addError($this, $exception, \PHP_Timer::stop());
            }
            \PHP_Timer::stop();
        }
        return $result;
    }

    /**
     * Set an indicator of whether or not the current run should run in a new process.
     *
     * @param bool $isParallelRun
     * @return void
     */
    public function setParallelRun($isParallelRun)
    {
        $this->isParallelRun = $isParallelRun;
    }

    /**
     * Avoid attempt to serialize a Closure
     *
     * @return array
     */
    public function __sleep()
    {
        return [];
    }

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
            $this->setResult($this->runTest());
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
            $this->eventManager->dispatchEvent(['failure'], [$e->getMessage()]);
        } catch (\Exception $e) {
            $this->status = \PHPUnit_Runner_BaseTestRunner::STATUS_ERROR;
            $this->statusMessage = $e->getMessage();
            $this->eventManager->dispatchEvent(['exception'], [$e->getMessage()]);
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

    /**
     * Set inIsolation parameter.
     *
     * @param  boolean $inIsolation
     * @throws \PHPUnit_Framework_Exception
     * @return void
     */
    public function setInIsolation($inIsolation)
    {
        parent::setInIsolation($inIsolation);
        if (is_bool($inIsolation)) {
            $this->inIsolation = $inIsolation;
        } else {
            throw \PHPUnit_Util_InvalidArgumentHelper::factory(1, 'boolean');
        }
    }

    /**
     * This method is a wrapper for the ini_set() function that automatically
     * resets the modified php.ini setting to its original value after the
     * test is run.
     *
     * @param  string $varName
     * @param  string $newValue
     * @throws \PHPUnit_Framework_Exception
     * @return void
     */
    protected function iniSet($varName, $newValue)
    {
        if (!is_string($varName)) {
            throw \PHPUnit_Util_InvalidArgumentHelper::factory(1, 'string');
        }

        $currentValue = ini_set($varName, $newValue);

        if ($currentValue !== false) {
            $this->iniSettings[$varName] = $currentValue;
        } else {
            throw new \PHPUnit_Framework_Exception(
                sprintf(
                    'INI setting "%s" could not be set to "%s".',
                    $varName,
                    $newValue
                )
            );
        }
    }

    /**
     * This method is a wrapper for the setlocale() function that automatically
     * resets the locale to its original value after the test is run.
     *
     * @throws \PHPUnit_Framework_Exception
     * @return void
     */
    protected function setLocale()
    {
        $args = func_get_args();

        if (count($args) < 2) {
            throw new \PHPUnit_Framework_Exception;
        }

        $category = $args[0];
        $locale = $args[1];

        $categories = [
            LC_ALL,
            LC_COLLATE,
            LC_CTYPE,
            LC_MONETARY,
            LC_NUMERIC,
            LC_TIME
        ];

        if (defined('LC_MESSAGES')) {
            $categories[] = LC_MESSAGES;
        }

        if (!in_array($category, $categories)) {
            throw new \PHPUnit_Framework_Exception;
        }

        if (!is_array($locale) && !is_string($locale)) {
            throw new \PHPUnit_Framework_Exception;
        }

        $this->locale[$category] = setlocale($category, null);

        $result = call_user_func_array('setlocale', $args);

        if ($result === false) {
            throw new \PHPUnit_Framework_Exception(
                'The locale functionality is not implemented on your platform, ' .
                'the specified locale does not exist or the category name is ' .
                'invalid.'
            );
        }
    }

    /**
     * Returns the status of this test.
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Returns the status message of this test.
     *
     * @return string
     */
    public function getStatusMessage()
    {
        return $this->statusMessage;
    }

    /**
     * Adds a value to the assertion counter.
     *
     * @param integer $count
     * @return void
     */
    public function addToAssertionCount($count)
    {
        $this->numAssertions += $count;
    }

    /**
     * Returns the number of assertions performed by this test.
     *
     * @return integer
     */
    public function getNumAssertions()
    {
        return $this->numAssertions;
    }

    /**
     * Get actual output.
     *
     * @return string
     */
    public function getActualOutput()
    {
        if (!$this->outputBufferingActive) {
            return $this->output;
        } else {
            return ob_get_contents();
        }
    }

    /**
     * Checks for the presence output data.
     *
     * @return boolean
     */
    public function hasOutput()
    {
        if (strlen($this->output) === 0) {
            return false;
        }

        if ($this->outputExpectedString !== null ||
            $this->outputExpectedRegex !== null ||
            $this->hasPerformedExpectationsOnOutput
        ) {
            return false;
        }

        return true;
    }

    /**
     * Expect output Regex.
     *
     * @param  string $expectedRegex
     * @throws \PHPUnit_Framework_Exception
     * @return void
     */
    public function expectOutputRegex($expectedRegex)
    {
        if ($this->outputExpectedString !== null) {
            throw new \PHPUnit_Framework_Exception;
        }

        if (is_string($expectedRegex) || is_null($expectedRegex)) {
            $this->outputExpectedRegex = $expectedRegex;
        }
    }

    /**
     * Expect output string.
     *
     * @param string $expectedString
     * @throws \PHPUnit_Framework_Exception
     * @return void
     */
    public function expectOutputString($expectedString)
    {
        if ($this->outputExpectedRegex !== null) {
            throw new \PHPUnit_Framework_Exception;
        }

        if (is_string($expectedString) || is_null($expectedString)) {
            $this->outputExpectedString = $expectedString;
        }
    }

    /**
     * Has performed expectations on output.
     *
     * @return bool
     */
    public function hasPerformedExpectationsOnOutput()
    {
        return $this->hasPerformedExpectationsOnOutput;
    }

    /**
     * Set Output Callback
     *
     * @param  callable $callback
     * @throws \PHPUnit_Framework_Exception
     * @return void
     */
    public function setOutputCallback($callback)
    {
        if (!is_callable($callback)) {
            throw \PHPUnit_Util_InvalidArgumentHelper::factory(1, 'callback');
        }

        $this->outputCallback = $callback;
    }

    /**
     * Returns a mock object for the specified class.
     *
     * @param  string $originalClassName Name of the class to mock.
     * @param  array|null $methods When provided, only methods whose names are in the array
     *         are replaced with a configurable test double. The behavior
     *         of the other methods is not changed.
     *         Providing null means that no methods will be replaced.
     * @param  array $arguments Parameters to pass to the original class' constructor.
     * @param  string $mockClassName Class name for the generated test double class.
     * @param  boolean $callOriginalConstructor Can be used to disable the call to the original class' constructor.
     * @param  boolean $callOriginalClone Can be used to disable the call to the original class' clone constructor.
     * @param  boolean $callAutoload Can be used to disable __autoload() during the generation of the test double class.
     * @param  boolean $cloneArguments
     * @param  boolean $callOriginalMethods
     * @return \PHPUnit_Framework_MockObject_MockObject
     * @throws \PHPUnit_Framework_Exception
     */
    public function getMock(
        $originalClassName,
        $methods = [],
        array $arguments = [],
        $mockClassName = '',
        $callOriginalConstructor = true,
        $callOriginalClone = true,
        $callAutoload = true,
        $cloneArguments = false,
        $callOriginalMethods = false
    ) {
        $mockObject = $this->getMockObjectGenerator()->getMock(
            $originalClassName,
            $methods,
            $arguments,
            $mockClassName,
            $callOriginalConstructor,
            $callOriginalClone,
            $callAutoload,
            $cloneArguments,
            $callOriginalMethods
        );

        $this->mockObjects[] = $mockObject;

        return $mockObject;
    }

    /**
     * Verifies the mock object expectations.
     *
     * @return void
     */
    protected function verifyMockObjects()
    {
        foreach ($this->mockObjects as $mockObject) {
            if ($mockObject->__phpunit_hasMatchers()) {
                $this->numAssertions++;
            }

            $mockObject->__phpunit_verify();
        }
    }

    /**
     * Get incomplete status of test.
     *
     * @return bool
     */
    public function getIsIncomplete()
    {
        return $this->isIncomplete;
    }

    /**
     * Set incomplete status of test.
     *
     * @param bool $status
     * @return void
     */
    public function setIsIncomplete($status)
    {
        $this->isIncomplete = $status;
    }
}

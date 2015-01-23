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
    private $data = [];

    /**
     * @var string
     */
    private $dataName = '';

    /**
     * The name of the test suite.
     *
     * @var    string
     */
    private $name = '';

    /**
     * The instance of the process manager.
     *
     * @var ProcessManager
     */
    private $processManager;

    /**
     * @var \Magento\Mtf\System\Event\EventManagerInterface
     */
    protected $eventManager;

    /**
     * @var bool
     */
    private static $codeGenerationFlag = false;

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

        $this->generateCode();

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
     * Run code generator if necessary
     *
     * @return void
     */
    protected function generateCode()
    {
        if (self::$codeGenerationFlag) {
            return;
        }
        self::$codeGenerationFlag = true;

        /** @var $generate \Magento\Mtf\Util\Generate\Page */
        $generator = $this->objectManager->get('Magento\Mtf\Util\Generate\Page');
        $generator->generateClasses();
        return;
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
}

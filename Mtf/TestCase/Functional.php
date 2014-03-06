<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\TestCase;

use Mtf\TestRunner\Process\ProcessManager;

/**
 * Class Functional
 *
 * Class is extended from PHPUnit_Framework_TestCase
 * Used for test cases based on old specification
 * "Injectable" abstract Class should be used instead
 *
 * @package Mtf\TestCase
 * @api
 * @abstract
 */
abstract class Functional extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Mtf\ObjectManager
     */
    protected $objectManager;

    /**
     * @var bool
     */
    protected $isParallelRun = false;

    /**
     * @var array
     */
    private $data = array();

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
     * Constructs a test case with the given name.
     *
     * @constructor
     * @param  string $name
     * @param  array  $data
     * @param  string $dataName
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

        $this->objectManager = \Mtf\ObjectManagerFactory::getObjectManager();

        $this->_construct();
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
     */
    public function run(\PHPUnit_Framework_TestResult $result = NULL)
    {
        if ($this->isParallelRun) {
            $params = array(
                'name' => $this->getName(false),
                'data' => $this->data,
                'dataName' => $this->dataName
            );
            $this->processManager->run($this, $result, $params);
        } else {
            parent::run($result);

            if ($this->processManager->isParallelModeSupported()) {
                $this->refineTestResult($result);
            }
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
     * Helper method to clear object manager from test results so that results can be serialized.
     *
     * @return void
     */
    protected function clearObjectManager() {
        $this->objectManager = null;
    }

    /**
     * Remove object manager from errors and failures so that results can be serialized.
     *
     * @param \PHPUnit_Framework_TestResult $result
     * @return void
     */
    private function refineTestResult(\PHPUnit_Framework_TestResult $result) {
        if (!$result->wasSuccessful()) {
            foreach ($result->failures() as $failure) {
                $failure->failedTest()->clearObjectManager();
            }
            foreach ($result->errors() as $error) {
                $error->failedTest()->clearObjectManager();
            }
        }
        if ($result->skippedCount() > 0) {
            foreach ($result->skipped() as $skipped) {
                $skipped->failedTest()->clearObjectManager();
            }
        }
    }
}

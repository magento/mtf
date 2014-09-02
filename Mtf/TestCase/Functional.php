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
     * @var \Mtf\System\Event\EventManagerInterface
     */
    protected $eventManager;

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

        $this->eventManager = $this->getObjectManager()->get('Mtf\System\Event\EventManagerInterface');

        $this->_construct();
    }

    /**
     * Get Object Manager instance
     *
     * @return \Mtf\ObjectManager
     */
    protected function getObjectManager()
    {
        if (!$this->objectManager) {
            $this->objectManager = \Mtf\ObjectManagerFactory::getObjectManager();
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
                parent::run($result);
                if ($this->getStatus() == \PHPUnit_Runner_BaseTestRunner::STATUS_ERROR) {
                    $this->eventManager->dispatchEvent(['exception'], [$this->getStatusMessage()]);
                }
                /** @var \PHPUnit_Framework_TestFailure $failure */
                foreach ($result->failures() as $failure) {
                    $this->eventManager->dispatchEvent(['failure'], [$failure->exceptionMessage()]);
                }
            } catch (\PHPUnit_Framework_Exception $phpUnitException) {
                $this->eventManager->dispatchEvent(['exception'], [$phpUnitException->getMessage()]);
                throw $phpUnitException;
            } catch (\Exception $exception) {
                $this->eventManager->dispatchEvent(['exception'], [$exception->getMessage()]);
                $this->fail($exception->getMessage());
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
     * Avoid attempt to serialize a Closure
     *
     * @return array
     */
    public function __sleep()
    {
        return [];
    }
}

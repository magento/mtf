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
abstract class Functional extends TestCase
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
                if ($this->getStatus() == \PHPUnit_Runner_BaseTestRunner::STATUS_ERROR) {
                    $this->eventManager->dispatchEvent(['exception'], [$this->getStatusMessage()]);
                }
            } catch (\PHPUnit_Framework_AssertionFailedError $phpUnitException) {
                $this->eventManager->dispatchEvent(['failure'], [$phpUnitException->getMessage()]);
                $result->addFailure($this, $phpUnitException, \PHP_Timer::stop());
            } catch (\Exception $exception) {
                $this->eventManager->dispatchEvent(['exception'], [$exception->getMessage()]);
                $result->addError($this, $exception, \PHP_Timer::stop());
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
}

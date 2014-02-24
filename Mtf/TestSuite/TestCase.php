<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\TestSuite;

use Mtf\ObjectManager;

/**
 * Class TestCase
 * This Test Suite class uses Test Case Iterator to collect appropriate Test Cases
 * as defined in TestCase Configuration
 *
 * @package Mtf\TestSuite
 * @api
 */
class TestCase extends \PHPUnit_Framework_TestSuite
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var TestSuiteFactory
     */
    protected $testSuiteFactory;

    /**
     * @var array
     */
    protected $callback;

    /**
     * @var array
     */
    protected $callbackArguments = [];

    /**
     * @constructor
     * @param string $theClass
     * @param string $name
     */
    public function __construct($theClass = '', $name = '')
    {
        $this->initObjectManager();

        $this->testSuiteFactory = $this->objectManager->get('Mtf\TestSuite\TestSuiteFactory');

        /** @var $testIterator \Mtf\Util\Iterator\TestCase */
        $testIterator = $this->objectManager->create('Mtf\Util\Iterator\TestCase');
        while ($testIterator->valid()) {
            $arguments = $testIterator->current();

            $class = $arguments['class'];
            $testCaseSuite = $this->testSuiteFactory->create($class, $arguments);
            $rule = $this->objectManager->get('Mtf\TestRunner\Rule\SuiteComposite');
            $allow = $rule->filterSuite($testCaseSuite);
            if ($allow) {
                $this->addTest($testCaseSuite, \PHPUnit_Util_Test::getGroups(
                    get_class($testCaseSuite), $testCaseSuite->getName()));
            }
            $testIterator->next();
        }

        parent::__construct($name);
    }

    /**
     * To execute callback if specified
     *
     * @param \PHPUnit_Framework_TestResult $result
     * @return \PHPUnit_Framework_TestResult
     */
    public function run(\PHPUnit_Framework_TestResult $result = null)
    {
        if ($this->callback) {
            call_user_func_array($this->callback, $this->callbackArguments);
        }

        return parent::run($result);
    }

    /**
     * Set callback
     *
     * @param array $callback
     * @param array $arguments
     */
    public function setCallback(array $callback, array $arguments = [])
    {
        $this->callback = $callback;
        $this->callbackArguments = $arguments;
    }

    /**
     * Initialize Object Manager
     */
    protected function initObjectManager()
    {
        if (!isset($this->objectManager)) {
            $this->objectManager = \Mtf\ObjectManager::getInstance();
        }
    }
}

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

namespace Mtf\TestSuite;

use Mtf\ObjectManager;
use Mtf\TestRunner\Process\ProcessManager;

/**
 * This Test Suite class uses Test Case Iterator to collect appropriate Test Cases
 * as defined in TestCase Configuration.
 *
 * @api
 */
class TestCase extends Injectable
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
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
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

            $factory = $this->testSuiteFactory;
            $testCallback = $this->objectManager->create(
                'Mtf\TestSuite\Callback',
                ['factory' => $factory, 'arguments' => $arguments, 'theClass' => $class]
            );
            $this->addTest($testCallback, \PHPUnit_Util_Test::getGroups($class));

            $testIterator->next();
        }

        parent::__construct($name);
    }

    /**
     * To execute callback if specified.
     *
     * @param \PHPUnit_Framework_TestResult $result
     * @return \PHPUnit_Framework_TestResult
     */
    public function run(\PHPUnit_Framework_TestResult $result = null)
    {
        if ($this->callback) {
            $processManager = ProcessManager::factory();
            if ($processManager->isParallelModeSupported()) {
                $processManager->applyAppState($this->callback, $this->callbackArguments);
            } else {
                call_user_func_array($this->callback, $this->callbackArguments);
            }
        }

        return parent::run($result);
    }

    /**
     * Set callback.
     *
     * @param array $callback
     * @param array $arguments
     * @return void
     */
    public function setCallback(array $callback, array $arguments = [])
    {
        $this->callback = $callback;
        $this->callbackArguments = $arguments;
    }

    /**
     * Initialize Object Manager.
     *
     * @return void
     */
    protected function initObjectManager()
    {
        if (!isset($this->objectManager)) {
            $this->objectManager = \Mtf\ObjectManager::getInstance();
        }
    }

    /**
     * Avoid attempt to serialize callback.
     *
     * @return array
     */
    public function __sleep()
    {
        return [];
    }
}

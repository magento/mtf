<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\TestSuite;

use Magento\Mtf\ObjectManager;
use Magento\Mtf\TestCase\Injectable as TestCaseInjectable;

/**
 * Class InjectableTestCase
 *
 * @api
 */
class InjectableTestCase extends Injectable
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @constructor
     * @param string $class
     * @param string $name
     * @param string $path
     */
    public function __construct($class = '', $name = '', $path = '')
    {
        // we don't need parent class to collect tests, so don't call parent constructor

        $this->initObjectManager();

        $name = $name ? $name : $class;
        $this->setName($name);

        if (is_string($class) && class_exists($class, false)) {
            $arguments = [
                'class' => $class,
                'path' => $path
            ];
            $theClass = new \ReflectionClass($class);
            foreach ($theClass->getMethods() as $method) {
                if (!$this->isPublicTestMethod($method)) {
                    continue;
                }
                $_arguments = $arguments;
                $methodName = $method->getName();
                $_arguments['name'] = $methodName;
                $test = $this->objectManager->create('Magento\Mtf\TestSuite\InjectableMethod', $_arguments);
                $this->addTest($test, \PHPUnit_Util_Test::getGroups($class, $methodName));
            }
        }

        $this->testCase = true;
    }

    /**
     * Set dependencies for tests
     *
     * @param array $dependencies
     * @return void
     */
    public function setDependencies(array $dependencies)
    {
        foreach ($this->tests as $test) {
            $test->setDependencies($dependencies);
        }
    }

    /**
     * Initialize ObjectManager
     *
     * @return void
     */
    protected function initObjectManager()
    {
        $this->objectManager = \Magento\Mtf\ObjectManager::getInstance();
    }

    /**
     * Validate if empty test methods
     *
     * @return bool
     */
    public function validate()
    {
        foreach ($this->tests as $method) {
            foreach ($method->tests as $methodTest) {
                if (!$methodTest instanceof TestCaseInjectable) {
                    return true;
                }

                /** @var $testVariationIterator \Magento\Mtf\Util\Iterator\TestCaseVariation */
                $testVariationIterator = $this->objectManager->create(
                    'Magento\Mtf\Util\Iterator\TestCaseVariation',
                    ['testCase' => $methodTest]
                );
                if (count($testVariationIterator) > 0) {
                    return true;
                }
            }
        }

        return false;
    }
}

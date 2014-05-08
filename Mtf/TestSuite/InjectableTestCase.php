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
 * Class InjectableTestCase
 *
 * @package Mtf\TestSuite
 * @api
 */
class InjectableTestCase extends TestSuite
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
                $test = $this->objectManager->create('Mtf\TestSuite\InjectableMethod', $_arguments);
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
        $this->objectManager = \Mtf\ObjectManager::getInstance();
    }
}

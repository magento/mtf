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
use Mtf\TestCase\Injectable as TestCaseInjectable;

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

                /** @var $testVariationIterator \Mtf\Util\Iterator\TestCaseVariation */
                $testVariationIterator = $this->objectManager->create(
                    'Mtf\Util\Iterator\TestCaseVariation',
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

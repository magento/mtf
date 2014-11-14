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

namespace Mtf\TestRunner\Rule;

use Mtf\TestSuite\InjectableTestCase;

/**
 * Class SuiteComposite
 *
 * @api
 */
class SuiteComposite extends AbstractRule implements SuiteRuleInterface
{
    /**
     * @var string
     */
    protected $class;

    /**
     * Apply filter to test suite according to rules
     *
     * @param \PHPUnit_Framework_TestSuite $testSuite
     * @return bool
     */
    public function filterSuite(\PHPUnit_Framework_TestSuite $testSuite)
    {
        $result = true;
        $classRule = $this->testRunnerConfig->getValue('suiteRule');
        if (isset($classRule['allow']['type'])) {
            if ($testSuite instanceof InjectableTestCase) {
                $type = 'injectable';
            } else {
                $type = 'regular';
            }
            $result = $type === $classRule['allow']['type'];
        }
        return $result;
    }

    /**
     * Apply Test Runner Configuration rules to check if Test Case is eligible for execution within Test Suite
     *
     * @param string $class
     * @return bool|mixed
     */
    public function apply($class)
    {
        $this->class = $class;
        $result = true;
        $rule = $this->testRunnerConfig->getValue('suiteRule');
        if ($rule) {
            $result = $this->processRule($rule);
        }
        return $result;
    }

    /**
     * Rule which verifies belonging to test group
     *
     * @param string $value
     * @return bool
     */
    protected function groupMatch($value)
    {
        return null;
    }

    /**
     * Rule which verifies belonging to namespace
     *
     * @param string $value
     * @return bool
     */
    protected function namespaceMatch($value)
    {
        if (strpos($this->class, $value) === 0) {
            return true;
        }
        return false;
    }

    /**
     * Rule which verifies belonging to module
     *
     * @param string $value
     * @return bool
     */
    protected function moduleMatch($value)
    {
        list($namespace, $module) = explode('\\', $this->class);
        $moduleName = $namespace . '_' . $module;
        if ($moduleName === $value) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Rule which verifies test case class
     *
     * @param string $value
     * @return bool
     */
    protected function classMatch($value)
    {
        return $this->class === $value;
    }
}

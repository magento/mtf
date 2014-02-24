<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\TestRunner\Rule;

use Mtf\TestSuite\InjectableTestCase;

/**
 * Class SuiteComposite
 *
 * @package Mtf\TestRunner\Rule
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
     * @param $value
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
     * @param $value
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

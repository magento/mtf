<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\TestRunner\Rule;

/**
 * Class SuiteRuleInterface
 *
 * @api
 */
interface SuiteRuleInterface
{
    /**
     * Apply Test Runner Configuration rules to check if Test Case is eligible for execution within Test Suite
     *
     * @param string $class
     * @return mixed
     */
    public function apply($class);

    /**
     * Apply filter to test suite according to rules
     *
     * @param \PHPUnit_Framework_TestSuite $testSuite
     * @return mixed
     */
    public function filterSuite(\PHPUnit_Framework_TestSuite $testSuite);
}

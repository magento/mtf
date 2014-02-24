<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\TestRunner\Rule;

/**
 * Class RuleInterface
 *
 * @package Mtf\TestRunner\Rule
 * @api
 */
interface ObjectRuleInterface
{
    /**
     * Apply Test Runner Configuration rules to check if Test Case is eligible for execution
     *
     * @param \PHPUnit_Framework_TestCase $testCase
     *
     * @return bool
     */
    public function apply(\PHPUnit_Framework_TestCase $testCase);
}

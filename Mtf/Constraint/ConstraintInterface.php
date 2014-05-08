<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Constraint;

/**
 * Interface for Constraint classes
 *
 * @package Mtf\Constraint
 * @api
 */
interface ConstraintInterface
{
    /**
     * Set Test Case and it's DI Arguments to Constraint
     *
     * @param \PHPUnit_Framework_TestCase $testCase
     * @param array $arguments
     * @return void
     */
    public function configure(
        \PHPUnit_Framework_TestCase $testCase,
        array $arguments = []
    );
}

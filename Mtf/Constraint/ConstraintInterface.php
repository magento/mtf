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
 * @api
 */
interface ConstraintInterface
{
    /**
     * Set Test Case and it's DI Arguments to Constraint
     *
     * @param array $arguments
     * @return void
     */
    public function configure(
        array $arguments = []
    );
}

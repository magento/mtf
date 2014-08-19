<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Constraint;

/**
 * Dummy Constraint
 */
class Dummy extends AbstractConstraint
{
    /**
     * Process assert actions
     *
     * @return bool
     */
    public function processAssert()
    {
        return true;
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Dummy Constraint';
    }

    /**
     * Don't count Dummy Constraint
     *
     * @return int
     */
    public function count()
    {
        return 0;
    }
}

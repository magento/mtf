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
 *
 * @package Mtf\Constraint
 */
class Dummy extends AbstractConstraint
{
    /**
     * {@inheritdoc}
     */
    public function processAssert()
    {
        return true;
    }

    /**
     * {@inheritdoc}
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

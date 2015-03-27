<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Constraint;

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

<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Constraint;

/**
 * Interface for Constraint classes
 *
 * @api
 */
interface ConstraintInterface
{
    /**
     * Set DI Arguments to Constraint
     *
     * @param array $arguments
     * @return void
     */
    public function configure(array $arguments = []);

    /**
     * Is Constraint enabled
     *
     * @return boolean
     */
    public function isActive();
}

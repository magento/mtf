<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Util\Filter;

/**
 * Interface for filter subject.
 */
interface FilterInterface
{
    /**
     * Apply filter to check if subject is eligible for execution.
     *
     * @param string $subject
     * @return bool
     */
    public function apply($subject);
}

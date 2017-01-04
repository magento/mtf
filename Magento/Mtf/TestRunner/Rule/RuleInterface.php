<?php
/**
 * Copyright © 2017 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\TestRunner\Rule;

/**
 * Interface for filtering rule.
 */
interface RuleInterface
{
    /**
     * Apply Test Runner Configuration rules to check if subject is eligible for execution.
     *
     * @param string $subject
     * @return bool
     */
    public function apply($subject);
}

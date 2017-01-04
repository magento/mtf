<?php
/**
 * Copyright © 2017 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\TestRunner\Rule;

use Magento\Mtf\Util\Filter\FilterInterface;

/**
 * Base class for applying Test Runner Configuration rule.
 */
class Rule
{
    /**
     * List applying filters.
     *
     * @var FilterInterface[]
     */
    protected $filters = [];

    /**
     * Add filter to rule.
     *
     * @param FilterInterface $filter
     * @return void
     */
    public function addFilter(FilterInterface $filter)
    {
        $this->filters[] = $filter;
    }

    /**
     * Get filters.
     *
     * @return FilterInterface[]
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Apply rule.
     *
     * @param string $class
     * @return bool
     */
    public function apply($class)
    {
        foreach ($this->filters as $filter) {
            if (!$filter->apply($class)) {
                return false;
            }
        }
        return true;
    }
}

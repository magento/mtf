<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Util\Filter;

/**
 * Base class filters out classes that are affected by specified class name.
 */
class AbstractClassName extends AbstractFilter implements FilterInterface
{
    /**
     * Filters out class.
     *
     * @param string $class
     * @return bool
     */
    public function apply($class)
    {
        if ($this->allow && !array_key_exists($class, $this->allow)) {
            return false;
        }
        if ($this->deny && array_key_exists($class, $this->deny)) {
            return false;
        }
        return true;
    }
}

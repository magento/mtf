<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Util\Filter;

/**
 * Base class filters out classes that are affected by specified modules.
 */
class AbstractClassModule extends AbstractFilter implements FilterInterface
{
    /**
     * Filters out class.
     *
     * @param string $class
     * @return bool
     */
    public function apply($class)
    {
        $module = $this->mapClassNameToModule($class);

        if ($this->allow && !array_key_exists($module, $this->allow)) {
            return false;
        }
        if ($this->deny && array_key_exists($module, $this->deny)) {
            return false;
        }
        return true;
    }
}

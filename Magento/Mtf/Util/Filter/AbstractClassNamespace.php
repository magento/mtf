<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Util\Filter;

/**
 * Base class filters out classes that are affected by namespace.
 */
abstract class AbstractClassNamespace extends AbstractFilter implements FilterInterface
{
    /**
     * Filters out class.
     *
     * @param string $class
     * @return bool
     */
    public function apply($class)
    {
        $namespace = $this->mapClassNameToNamespace($class);

        if ($this->allow && !array_key_exists($namespace, $this->allow)) {
            return false;
        }
        if ($this->deny && array_key_exists($namespace, $this->deny)) {
            return false;
        }
        return true;
    }
}

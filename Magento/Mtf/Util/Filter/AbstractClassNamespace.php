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
        $testStatus = true;

        if ($this->allow && is_array($this->allow)) {
            foreach (array_keys($this->allow) as $allow) {
                if ($namespace === trim($allow, '\\')) {
                    $testStatus = true;
                    break;
                }
                $testStatus = false;
            }
        }
        if ($this->deny && is_array($this->deny)) {
            foreach (array_keys($this->deny) as $deny) {
                if ($namespace === trim($deny, '\\')) {
                    $testStatus = false;
                }
            }
        }

        return $testStatus;
    }
}

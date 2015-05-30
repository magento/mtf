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
        $testStatus = true;

        if ($this->allow && is_array($this->allow)) {
            foreach ($this->allow as $allow) {
                if ($class === trim($allow['value'], '\\')) {
                    $testStatus = true;
                    break;
                }
                $testStatus = false;
            }
        }
        if ($this->deny && is_array($this->deny)) {
            foreach ($this->deny as $deny) {
                if ($class === trim($deny['value'], '\\')) {
                    $testStatus = false;
                }
            }
        }
        return $testStatus;
    }
}

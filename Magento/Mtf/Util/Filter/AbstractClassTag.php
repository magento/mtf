<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Util\Filter;

/**
 * Base class filters out classes that are affected by specified tag.
 */
class AbstractClassTag extends AbstractFilterTag implements FilterInterface
{
    /**
     * Filters out class.
     *
     * @param string $class
     * @return bool
     */
    public function apply($class)
    {
        $tags = $this->getClassTags($class);
        return $this->processApply($tags);
    }

    /**
     * Return constants of class.
     *
     * @param string $class
     * @return array
     */
    protected function getClassTags($class)
    {
        $reflection  = new \ReflectionClass($class);
        $constants = $reflection->getConstants();
        $result = [];

        foreach ($constants as $name => $constant) {
            $name = strtolower($name);
            $values = empty($constant) ? [] : explode(',', $constant);

            $result[$name] = array_map('trim', $values);
        }

        return $result;
    }
}

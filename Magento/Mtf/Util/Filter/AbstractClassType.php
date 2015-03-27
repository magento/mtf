<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Util\Filter;

/**
 * Base class filters out classes that are affected by type.
 */
class AbstractClassType extends AbstractFilterTag implements FilterInterface
{
    /**
     * Mapping type of classes.
     *
     * @var array
     */
    protected $mapTypes = [];

    /**
     * Filters out class.
     *
     * @param string $class
     * @return bool
     */
    public function apply($class)
    {
        if ($this->allow && !$this->inTypes($class, $this->allow)) {
            return false;
        }
        if ($this->deny && $this->inTypes($class, $this->deny)) {
            return false;
        }

        return true;
    }

    /**
     * Checks that type exists in stack of types.
     *
     * @param string $needleType
     * @param array $stackTypes
     * @return bool
     */
    protected function inTypes($needleType, array $stackTypes)
    {
        $needleClass = new \ReflectionClass($needleType);

        foreach ($stackTypes as $stackType) {
            $stackTypeClass = $this->mapClassNameToType($stackType);
            if ($this->isReflectionClassInstanceOf($needleClass, $stackTypeClass)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether a reflection class is an instantiated object of a certain class.
     *
     * @param \ReflectionClass $reflectionClass
     * @param string $className
     * @return bool
     */
    protected function isReflectionClassInstanceOf(\ReflectionClass $reflectionClass, $className)
    {
        $parent = $reflectionClass->getParentClass();
        $isInstance = false;

        while ($parent && !$isInstance) {
            $isInstance = $className == $parent->getName();
            $parent = $parent->getParentClass();
        }

        return $isInstance;
    }

    /**
     * Mapping type to class name.
     *
     * @param string $type
     * @return string
     */
    protected function mapClassNameToType($type)
    {
        if (!isset($this->mapTypes[$type])) {
            $classType = 'Magento\Mtf\TestCase\\' . ucfirst($type);
            $this->mapTypes[$type] = $classType;
        }

        return $this->mapTypes[$type];
    }
}

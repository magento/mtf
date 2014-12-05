<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mtf\Util\Filter;

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
            $classType = 'Mtf\TestCase\\' . ucfirst($type);
            $this->mapTypes[$type] = $classType;
        }

        return $this->mapTypes[$type];
    }
}

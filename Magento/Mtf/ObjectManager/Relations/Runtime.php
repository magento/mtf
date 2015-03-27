<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\ObjectManager\Relations;

/**
 * Class Runtime
 */
class Runtime implements \Magento\Mtf\ObjectManager\RelationsInterface
{
    /**
     * @var \Magento\Mtf\Code\Reader\ClassReader
     */
    protected $_classReader;

    /**
     * Default behavior
     *
     * @var array
     */
    protected $_default = [];

    /**
     * @param \Magento\Mtf\Code\Reader\ClassReader $classReader
     */
    public function __construct(\Magento\Mtf\Code\Reader\ClassReader $classReader = null)
    {
        $this->_classReader = $classReader ? : new \Magento\Mtf\Code\Reader\ClassReader();
    }

    /**
     * Check whether requested type is available for read
     *
     * @param string $type
     * @return bool
     */
    public function has($type)
    {
        return class_exists($type) || interface_exists($type);
    }

    /**
     * Retrieve list of parents
     *
     * @param string $type
     * @return array
     */
    public function getParents($type)
    {
        if (!class_exists($type)) {
            return $this->_default;
        }
        return $this->_classReader->getParents($type) ? : $this->_default;
    }
}

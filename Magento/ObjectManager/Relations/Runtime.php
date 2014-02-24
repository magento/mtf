<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\ObjectManager\Relations;

class Runtime implements \Magento\ObjectManager\Relations
{
    /**
     * @var \Magento\Code\Reader\ClassReader
     */
    protected $_classReader;

    /**
     * Default behavior
     *
     * @var array
     */
    protected $_default = array();

    /**
     * @param \Magento\Code\Reader\ClassReader $classReader
     */
    public function __construct(\Magento\Code\Reader\ClassReader $classReader = null)
    {
        $this->_classReader = $classReader ?: new \Magento\Code\Reader\ClassReader();
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
        return $this->_classReader->getParents($type) ?: $this->_default;
    }
}

<?php
/**
 * List of parent classes with their parents and interfaces
 *
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\ObjectManager\Relations;

class Compiled implements \Magento\ObjectManager\Relations
{
    /**
     * List of class relations
     *
     * @var array
     */
    protected $_relations;

    /**
     * Default relation list
     *
     * @var array
     */
    protected $_default = array();

    /**
     * @param array $relations
     */
    public function __construct(array $relations)
    {
        $this->_relations = $relations;
    }

    /**
     * Check whether requested type is available for read
     *
     * @param string $type
     * @return bool
     */
    public function has($type)
    {
        return isset($this->_relations[$type]);
    }

    /**
     * Retrieve parents for class
     *
     * @param string $type
     * @return array
     */
    public function getParents($type)
    {
        return $this->_relations[$type];
    }
}

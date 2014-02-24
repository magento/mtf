<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Framework
 * @subpackage  Config
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Magento configuration DOM factory
 */
namespace Magento\Config;

class DomFactory
{

    const CLASS_NAME = 'Magento\Config\Dom';

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManger
     */
    public function __construct(\Magento\ObjectManager $objectManger)
    {
        $this->_objectManager = $objectManger;
    }

    /**
     * Create DOM object
     *
     * @param array $arguments
     * @return \Magento\Config\Dom
     */
    public function createDom(array $arguments = array())
    {
        return $this->_objectManager->create(self::CLASS_NAME, $arguments);
    }
}

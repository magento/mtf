<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ObjectManager\Config\Argument;

use Magento\ObjectManager;
use Magento\ObjectManager\Config;

/**
 * Factory that creates an instance by a type name taking into account whether it's shared or not
 */
class ObjectFactory
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @param Config $config
     * @param ObjectManager $objectManager
     */
    public function __construct(Config $config, ObjectManager $objectManager = null)
    {
        $this->config = $config;
        $this->objectManager = $objectManager;
    }

    /**
     * Assign object manager instance
     *
     * @param ObjectManager $objectManager
     */
    public function setObjectManager(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Retrieve instance of the object manager
     *
     * @return ObjectManager
     * @throws \LogicException
     */
    public function getObjectManager()
    {
        if (!$this->objectManager) {
            throw new \LogicException('Object manager has not been assigned yet.');
        }
        return $this->objectManager;
    }

    /**
     * Return new or shared instance of a given type
     *
     * @param string $type
     * @param bool|null $isShared NULL - use the sharing configuration
     * @return object
     */
    public function create($type, $isShared = null)
    {
        $objectManager = $this->getObjectManager();
        $isShared = isset($isShared) ? $isShared : $this->config->isShared($type);
        $result = $isShared ? $objectManager->get($type) : $objectManager->create($type);
        return $result;
    }
}

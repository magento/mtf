<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\ObjectManager;

interface Config
{
    /**
     * Set class relations
     *
     * @param \Magento\ObjectManager\Relations $relations
     */
    public function setRelations(Relations $relations);

    /**
     * Set configuration cache instance
     *
     * @param \Magento\ObjectManager\ConfigCache $cache
     */
    public function setCache(ConfigCache $cache);

    /**
     * Retrieve list of arguments per type
     *
     * @param string $type
     * @param array $arguments
     * @return array
     */
    public function getArguments($type, $arguments);

    /**
     * Check whether type is shared
     *
     * @param string $type
     * @return bool
     */
    public function isShared($type);

    /**
     * Retrieve instance type
     *
     * @param string $instanceName
     * @return mixed
     */
    public function getInstanceType($instanceName);

    /**
     * Retrieve preference for type
     *
     * @param string $type
     * @return string
     * @throws \LogicException
     */
    public function getPreference($type);

    /**
     * Extend configuration
     *
     * @param array $configuration
     */
    public function extend(array $configuration);
}

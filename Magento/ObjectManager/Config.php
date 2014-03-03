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
     * @param Relations $relations
     *
     * @return void
     */
    public function setRelations(Relations $relations);

    /**
     * Set configuration cache instance
     *
     * @param ConfigCache $cache
     *
     * @return void
     */
    public function setCache(ConfigCache $cache);

    /**
     * Retrieve list of arguments per type
     *
     * @param string $type
     * @return array
     */
    public function getArguments($type);

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

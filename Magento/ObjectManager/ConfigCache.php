<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\ObjectManager;

interface ConfigCache
{
    /**
     * Retrieve configuration from cache
     *
     * @param string $key
     * @return array
     */
    public function get($key);

    /**
     * Save config to cache
     *
     * @param array $config
     * @param string $key
     */
    public function save(array $config, $key);
}

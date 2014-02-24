<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Config;

interface DataInterface
{
    /**
     * Merge config data to the object
     *
     * @param array $config
     */
    public function merge(array $config);

    /**
     * Get config value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null);
}

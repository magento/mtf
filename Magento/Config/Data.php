<?php
/**
 * Config data. Represents loaded and cached configuration data. Should be used to gain access to different types
 *
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Config;

class Data implements \Magento\Config\DataInterface
{
    /**
     * Configuration reader model
     *
     * @var \Magento\Config\ReaderInterface
     */
    protected $_reader;

    /**
     * Configuration cache model
     *
     * @var \Magento\Config\CacheInterface
     */
    protected $_cache;


    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheId;

    /**
     * Config data
     *
     * @var array
     */
    protected $_data = array();

    /**
     * @param \Magento\Config\ReaderInterface $reader
     * @param \Magento\Config\CacheInterface $cache
     * @param $cacheId
     */
    public function __construct(
        \Magento\Config\ReaderInterface $reader,
        \Magento\Config\CacheInterface $cache,
        $cacheId
    ) {
        $data = $cache->load($cacheId);
        if (false === $data) {
            $data = $reader->read();
            $cache->save(serialize($data), $cacheId);
        } else {
            $data = unserialize($data);
        }
        $this->merge($data);
    }

    /**
     * Merge config data to the object
     *
     * @param array $config
     */
    public function merge(array $config)
    {
        $this->_data = array_replace_recursive($this->_data, $config);
    }

    /**
     * Get config value by key
     *
     * @param string $path
     * @param null $default
     * @return mixed
     */
    public function get($path = null, $default = null)
    {
        if ($path === null) {
            return $this->_data;
        }
        $keys = explode('/', $path);
        $data = $this->_data;
        foreach ($keys as $key) {
            if (is_array($data) && array_key_exists($key, $data)) {
                $data = $data[$key];
            } else {
                return $default;
            }
        }
        return $data;
    }
}

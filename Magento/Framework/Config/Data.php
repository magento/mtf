<?php
/**
 * Config data. Represents loaded and cached configuration data. Should be used to gain access to different types
 *
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Framework\Config;

class Data implements \Magento\Framework\Config\DataInterface
{
    /**
     * Configuration reader model
     *
     * @var \Magento\Framework\Config\ReaderInterface
     */
    protected $_reader;

    /**
     * Configuration cache model
     *
     * @var \Magento\Framework\Config\CacheInterface
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
     * Constructor
     *
     * @param \Magento\Framework\Config\ReaderInterface $reader
     * @param \Magento\Framework\Config\CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        \Magento\Framework\Config\ReaderInterface $reader,
        \Magento\Framework\Config\CacheInterface $cache,
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
     * @return void
     */
    public function merge(array $config)
    {
        $this->_data = array_replace_recursive($this->_data, $config);
    }

    /**
     * Get config value by key
     *
     * @param string $path
     * @param mixed $default
     * @return array|mixed|null
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

<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Mtf\System\Event;

use Mtf\System\Event\Config\Reader;
use Magento\Framework\Config\Data;

/**
 * Class for dealing wth events.xml config file and adoption it as Config to an application
 */
class Config extends Data
{
    /**
     * Configuration reader model
     *
     * @var \Magento\Framework\Config\ReaderInterface
     */
    protected $reader;

    /**
     * Constructor
     * @param Reader $reader
     */
    public function __construct(
        Reader $reader
    ) {
        $this->reader = $reader;
        $data = $reader->read();
        $this->merge($data);
    }

    /**
     * Config data
     *
     * @var array
     */
    protected $_data = array();

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
     * @return array|string|null
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

    /**
     * Returns observers
     *
     * @return array
     */
    public function getObservers()
    {
        $observers = [];
        foreach ($this->get('config') as $metadata) {
            foreach($metadata['observer'] as $observer) {
                foreach($observer['tag'] as $tag) {
                    $observers[$observer['class']][] = $tag['pattern'];
                }
            }
        }
        return $observers;
    }
}

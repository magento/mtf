<?php
/**
 * Config data. Represents loaded and cached configuration data. Should be used to gain access to different types
 *
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace Mtf\Config;

/**
 * Class Data
 */
class Data implements \Mtf\Config\DataInterface
{
    /**
     * Configuration reader model
     *
     * @var \Mtf\Config\ReaderInterface
     */
    protected $reader;

    /**
     * Config data
     *
     * @var array
     */
    protected $data = [];

    /**
     * Constructor
     *
     * @param \Mtf\Config\ReaderInterface $reader
     * @param \Mtf\Config\CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        \Mtf\Config\ReaderInterface $reader
    ) {
        $this->reader = $reader;
        $this->load();
    }

    /**
     * Merge config data to the object
     *
     * @param array $config
     * @return void
     */
    public function merge(array $config)
    {
        $this->data = array_replace_recursive($this->data, $config);
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
            return $this->data;
        }
        $keys = explode('/', $path);
        $data = $this->data;
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
     * Set name of the config file
     *
     * @param string $fileName
     * @return self
     */
    public function setFileName($fileName)
    {
        if (!is_null($fileName)) {
            $this->reader->setFileName($fileName);
        }
        return $this;
    }

    /**
     * Load config data
     *
     * @param string|null $scope
     */
    public function load($scope = null)
    {
        $this->merge(
            $this->reader->read($scope)
        );
    }
}
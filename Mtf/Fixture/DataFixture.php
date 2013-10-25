<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Fixture;

use Mtf\Fixture;
use Mtf\Repository\AbstractRepository;
use Mtf\System\Config;

/**
 * Class DataFixture
 *
 * Ensures correct data representation between the system under test and testing framework
 *
 * @package Mtf\Fixture
 */
abstract class DataFixture implements Fixture
{
    /**
     * Fixture data
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Fixture default data configuration
     *
     * @var array
     */
    protected $_defaultConfig = array();

    /**
     * Fixture Data Configuration
     *
     * @var array
     */
    protected $_dataConfig = array();

    /**
     * Array of placeholders applied on data
     *
     * @var array
     */
    protected $_placeholders = array();

    /**
     * Configuration instance
     *
     * @var Config
     */
    protected $_configuration;

    /**
     * Fixture Repository
     *
     * @var AbstractRepository
     */
    protected $_repository;

    /**
     * Constructor
     *
     * @constructor
     * @param Config $configuration
     * @param array $placeholders
     */
    public function __construct(Config $configuration, array $placeholders = array())
    {
        $this->_configuration = $configuration;

        $this->_placeholders = $this->_mergePlaceholders($placeholders);

        $this->_initData();

        $this->_applyPlaceholders($this->_data, array('isolation' => mt_rand()));
        $this->_applyPlaceholders($this->_data, $this->_placeholders);
        $this->_placeholders = array();
    }

    /**
     * Initialize fixture data
     */
    abstract protected function _initData();

    /**
     * Persists prepared data into application
     *
     * @throws \BadMethodCallException
     */
    public function persist()
    {
        throw new \BadMethodCallException('Not implemented yet');
    }

    /**
     * Return prepared data set.
     *
     * @param $key [optional]
     * @return mixed
     * @throws \RuntimeException
     */
    public function getData($key = null)
    {
        // apply placeholders if available
        $this->_applyPlaceholders($this->_data, $this->_placeholders);
        $this->_placeholders = array();

        if (empty($this->_data)) {
            throw new \RuntimeException('Data must be set');
        }

        if ($key === null) {
            return $this->_data;
        }

        /* process a/b/c key as ['a']['b']['c'] */
        if (strpos($key, '/')) {
            $data = $this->getDataByPath($key);
        } else {
            $data = $this->getDataByKey($key);
        }

        return $data;
    }

    /**
     * Get object data by path
     *
     * Method consider the path as chain of keys: a/b/c => ['a']['b']['c']
     *
     * @param string $path
     * @return mixed
     */
    protected function getDataByPath($path)
    {
        $keys = explode('/', $path);

        $data = $this->_data;
        foreach ($keys as $key) {
            if (is_array($data) && isset($data[$key])) {
                $data = $data[$key];
            } else {
                return null;
            }
        }

        return $data;
    }

    /**
     * Get object data by particular key
     *
     * @param string $key
     * @return mixed
     */
    protected function getDataByKey($key)
    {
        return isset($this->_data[$key]) ? $this->_data[$key] : null;
    }

    /**
     * Return data set configuration settings
     *
     * @return string
     */
    public function getDataConfig()
    {
        return $this->_dataConfig;
    }

    /**
     * Merge with existed placeholders
     *
     * @param array $placeholders
     * @return array
     */
    protected function _mergePlaceholders(array $placeholders)
    {
        return array_merge($this->_placeholders, $placeholders);
    }

    /**
     * Recursively apply placeholders to each data element
     *
     * @param array $data
     * @param array $placeholders
     */
    protected function _applyPlaceholders(array & $data, array $placeholders)
    {
        if ($placeholders) {
            $replacePairs = array();
            foreach ($placeholders as $pattern => $replacement) {
                $replacePairs['%' . $pattern . '%'] = $replacement;
            }
            $callback = function (&$value) use ($replacePairs) {
                foreach ($replacePairs as $pattern => $replacement) {
                    if (strpos($value, $pattern) !== false) {
                        if (is_callable($replacement)) {
                            $param = trim($pattern, '%');
                            $replacement = $replacement($param);
                        }

                        $value = str_replace($pattern, $replacement, $value);
                    }
                }
            };
            array_walk_recursive($data, $callback);
        }
    }

    /**
     * Switch current data set.
     *
     * @param $name
     * @return bool
     */
    public function switchData($name)
    {
        if (!isset($this->_repository)) {
            return false;
        }

        $dataSet = $this->_repository->get($name);
        if (empty($dataSet)) {
            return true;
        }

        $this->_data = isset($dataSet['data']) ? $dataSet['data'] : array();
        $this->_dataConfig = isset($dataSet['config']) ? $dataSet['config'] : array();

        $this->_applyPlaceholders($this->_data, array('isolation' => mt_rand()));
        $this->_applyPlaceholders($this->_data, $this->_placeholders);

        return true;
    }
}

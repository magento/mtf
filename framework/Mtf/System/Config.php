<?php
/**
 * {license_notice}
 *
 * @api
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\System;

use Symfony\Component\Yaml\Yaml;

/**
 * Class Config
 *
 * @package Mtf\System
 */
class Config
{
    /**
     * Container for parameters
     *
     * @var
     */
    protected $_params;

    /**
     * Reads input file with configuration if not empty $filePath
     *
     * @param null|string $filePath
     */
    public function __construct($filePath = null)
    {
        $this->_init($filePath);
    }

    /**
     * Read configuration files
     *
     * @param string $filePath
     * @throws \Exception
     */
    protected function _init($filePath)
    {
        if (is_string($filePath)) {
            $paths = array($filePath);
        } else {
            $paths = array(
                self::getEnvironmentValue('server_config_path'),
                self::getEnvironmentValue('app_config_path'),
                self::getEnvironmentValue('isolation_config_path'),
                self::getEnvironmentValue('handlers_config_path')
            );
        }

        foreach ($paths as $path) {
            if (!file_exists($path)) {
                throw new \Exception(
                    'Configuration file ' . $path . ' cannot be found!'
                );
            }
            list ($prefix, $suffix) = explode('.yml', $path);
            $prefix = str_replace('\\', '/', $prefix);
            $prefix = explode('/', $prefix);
            $configurationScope = array_pop($prefix);
            $this->_params[$configurationScope] = Yaml::parse($path);
        }
    }

    /**
     * Get parameters
     *
     * @param null|string $key
     * @return array|string|null
     */
    public function getConfigParam($key = null)
    {
        if (null === $key) {
            $param = $this->_params;
        } elseif (strpos($key, '/')) {
            $param = $this->getParamByPath($key);
        } elseif (isset($this->_params[$key])) {
            $param = $this->_params[$key];
        } else {
            $param = null;
        }

        return $param;
    }

    /**
     * Get parameters by path
     *
     * Method consider the path as chain of keys: a/b/c => ['a']['b']['c']
     *
     * @param string $path
     * @return mixed
     */
    public function getParamByPath($path)
    {
        $keys = explode('/', $path);

        $data = $this->_params;
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
     * Return value from $_ENV container
     *
     * @param string $param
     * @return null|string
     */
    public static function getEnvironmentValue($param)
    {
        if (!isset($_ENV[$param])) {
            return null;
        }
        return $_ENV[$param];
    }
}

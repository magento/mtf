<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\System;

use Symfony\Component\Yaml\Yaml;

/**
 * Class Config
 *
 * @api
 */
class Config
{
    /**
     * Container for parameters
     *
     * @var array
     */
    protected $_params;

    /**
     * Reads input file with configuration if not empty $filePath
     *
     * @constructor
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
     * @return void
     * @throws \Exception
     */
    protected function _init($filePath)
    {
        if (is_string($filePath)) {
            $paths = [$filePath];
        } else {
            $paths = [
                self::getEnvironmentValue('server_config_path', 'config/server.yml.dist'),
                self::getEnvironmentValue('app_config_path', 'config/application.yml.dist'),
                self::getEnvironmentValue('isolation_config_path', 'config/isolation.yml.dist'),
                self::getEnvironmentValue('handlers_config_path', 'config/handler.yml.dist'),
            ];
        }

        foreach ($paths as $path) {
            if (!file_exists(MTF_BP . '/' . $path)) {
                throw new \Exception(
                    'Configuration file "' . $path . '" cannot be found!'
                );
            }
            list ($prefix, $suffix) = explode('.yml', $path);
            $prefix = str_replace('\\', '/', $prefix);
            $prefix = explode('/', $prefix);
            $configurationScope = array_pop($prefix);
            $this->_params[$configurationScope] = Yaml::parse(MTF_BP . '/' . $path);
        }
    }

    /**
     * Get parameters
     *
     * @param null|string $key
     * @param null|string $default
     * @return array|string|null
     */
    public function getConfigParam($key = null, $default = null)
    {
        if (null === $key) {
            $param = isset($this->_params) ? $this->_params : $default;
        } elseif (strpos($key, '/')) {
            $param = $this->getParamByPath($key, $default);
        } elseif (isset($this->_params[$key])) {
            $param = $this->_params[$key];
        } else {
            $param = $default;
        }

        return $param;
    }

    /**
     * Get parameters by path
     *
     * Method consider the path as chain of keys: a/b/c => ['a']['b']['c']
     *
     * @param string $path
     * @param null|string $default
     * @return mixed
     */
    public function getParamByPath($path, $default = null)
    {
        $keys = explode('/', $path);

        $data = $this->_params;
        foreach ($keys as $key) {
            if (is_array($data) && isset($data[$key])) {
                $data = $data[$key];
            } else {
                return $default;
            }
        }
        return $data;
    }

    /**
     * Return value from $_ENV container
     *
     * @param string $param
     * @param string $default
     * @return null|string
     */
    public static function getEnvironmentValue($param, $default = null)
    {
        if (!isset($_ENV[$param])) {
            return $default;
        }
        return $_ENV[$param];
    }
}

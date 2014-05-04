<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\TestRunner;

use Mtf\TestRunner\Configuration\Reader;

/**
 * Class Configuration
 *
 * @package Mtf\TestRunner
 * @api
 */
class Configuration
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * Load configuration
     *
     * @param string $configFilePath
     * @return void
     */
    public function load($configFilePath)
    {
        $reader = new Reader();
        $this->data = $reader->read($configFilePath);
    }

    /**
     * Get configuration value
     *
     * @param null|string $key
     * @param null|string $default
     * @return array|string|null
     */
    public function getValue($key = null, $default = null)
    {
        if (null === $key) {
            $param = isset($this->data) ? $this->data : $default;
        } elseif (strpos($key, '/')) {
            $param = $this->getParamByPath($key, $default);
        } elseif (isset($this->data[$key])) {
            $param = $this->data[$key];
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
    private function getParamByPath($path, $default = null)
    {
        $keys = explode('/', $path);

        $data = $this->data;
        foreach ($keys as $key) {
            if (is_array($data) && isset($data[$key])) {
                $data = $data[$key];
            } else {
                return $default;
            }
        }
        return $data;
    }
}

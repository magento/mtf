<?php
/**
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
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function _init($filePath)
    {
        $paths = [];
        if (is_string($filePath)) {
            $paths = [$filePath];
        } else {
            foreach ($_ENV as $key => $value) {
                if (strpos($key, 'config_path')) {
                    $paths[] = $value;
                }
            }
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

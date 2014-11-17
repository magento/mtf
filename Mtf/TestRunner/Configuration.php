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

namespace Mtf\TestRunner;

use Mtf\TestRunner\Configuration\Reader;

/**
 * Class Configuration
 *
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

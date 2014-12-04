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
 * Loader test runner configuration.
 *
 * @api
 */
class Configuration
{
    /**
     * Configuration data.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Configuration reader.
     *
     * @var Reader
     */
    protected $reader;

    /**
     * @constructor
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * Load configuration.
     *
     * @param string $configFolderPath
     * @return void
     */
    public function load($configFolderPath)
    {
        $this->data = $this->reader->read($configFolderPath);
        if (isset($this->data['rule'])) {
            $this->data['rule'] = $this->prepareRule($this->data['rule']);
        }
    }

    /**
     * Get configuration value.
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
     * Get parameters by path.
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

    /**
     * Prepare rule configuration.
     *
     * Swaps "access"(allow, deny) and "filter name" level
     *
     * @param array $rules
     * @return array
     */
    protected function prepareRule(array $rules)
    {
        $result = [];

        foreach ($rules as $ruleName => $rule) {
            foreach ($rule as $accessName => $access) {
                foreach ($access as $filterName => $filter) {
                    $result[$ruleName][$filterName][$accessName] = $filter;
                }
            }
        }

        return $result;
    }
}

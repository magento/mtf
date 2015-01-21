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

namespace Magento\Mtf\TestRunner;

/**
 * Loader test runner configuration.
 *
 * @api
 */
class Configuration
{
    /**
     * Environment field name for allow module list
     */
    const MODULE_FILTER = 'module_filter';

    /**
     * Environment field name for type filtering modules.
     */
    const MODULE_FILTER_STRICT = 'module_filter_strict';

    /**
     * Configuration data.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Configuration model.
     *
     * @var \Magento\Mtf\Config\DataInterface
     */
    protected $configuration;

    /**
     * @constructor
     * @param \Magento\Mtf\Config\Data $configuration
     */
    public function __construct(\Magento\Mtf\Config\Data $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Load configuration.
     *
     * @param string $configFileName
     * @return void
     */
    public function load($configFileName)
    {
        $this->configuration->load($configFileName);
        $this->data = $this->configuration->get();
        if (isset($this->data['rule'])) {
            $this->data['rule'] = $this->prepareRule($this->data['rule']);
        }
    }

    /**
     * Load configuration from environment.
     *
     * @return void
     */
    public function loadEnvConfig()
    {
        $modules = getenv(self::MODULE_FILTER);
        $strict = getenv(self::MODULE_FILTER_STRICT);

        if ($modules) {
            $this->data['rule']['testsuite']['module'] = [];

            $modules = array_map('trim', explode(',', $modules));
            $strict = (false === $strict) ? 1 : $strict;
            foreach ($modules as $module) {
                $this->data['rule']['testsuite']['module']['allow'][$module] = $strict;
            }

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

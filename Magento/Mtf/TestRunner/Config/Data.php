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

namespace Magento\Mtf\TestRunner\Config;

/**
 * Loader test runner configuration.
 *
 * @api
 */
class Data extends \Magento\Mtf\Config\Data
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
     * @constructor
     * @param \Magento\Mtf\Config\ReaderInterface $reader
     */
    public function __construct(
        \Magento\Mtf\Config\ReaderInterface $reader
    )
    {
        parent::__construct($reader);
    }

    /**
     * Load config data
     *
     * @param string|null $scope
     */
    public function load($scope = null)
    {
        parent::load($scope);

        if (isset($this->data['rule'])) {
            $this->data['rule'] = $this->prepareRule($this->data['rule']);
        }

        $this->loadEnvConfig();
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

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

namespace Mtf\TestRunner\Rule;

use Mtf\ObjectManager;
use Mtf\Config;

/**
 * Factory for create rules.
 */
class RuleFactory
{
    /**
     * Object manager.
     *
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Data configuration.
     *
     * @var array
     */
    protected $config;

    /**
     * @constructor
     * @param ObjectManager $objectManager
     * @param Config $config
     */
    public function __construct(ObjectManager $objectManager, Config $config)
    {
        $configFilePath = __DIR__ . '/etc/rule.xml';

        $this->objectManager = $objectManager;
        $this->config = $config->getData('test_runner_rule', $configFilePath);
    }

    /**
     * Create rule by scope.
     *
     * @param string $scope
     * @return Rule
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function create($scope)
    {
        if (!isset($this->config[$scope])) {
            throw new \Exception("Can't find rule with scope \"{$scope}\"");
        }

        /** @var \Mtf\TestRunner\Rule\Rule $rule */
        $rule = $this->objectManager->create('\Mtf\TestRunner\Rule\Rule');
        $filters = $this->config[$scope];

        foreach ($filters as $type => $class) {
            $filter = $this->objectManager->create($class);
            $rule->addFilter($filter);
        }

        return $rule;
    }
}

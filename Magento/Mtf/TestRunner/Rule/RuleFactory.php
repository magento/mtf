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

namespace Magento\Mtf\TestRunner\Rule;

use Magento\Mtf\ObjectManager;
use Magento\Mtf\Config;

/**
 * Factory for create rules.
 */
class RuleFactory
{
    /**
     * Object manager.
     *
     * @var \Magento\Mtf\ObjectManager
     */
    protected $objectManager;

    /**
     * Configuration data.
     *
     * @var array
     */
    protected $config;

    /**
     * @constructor
     * @param \Magento\Mtf\ObjectManager $objectManager
     * @param \Magento\Mtf\Config\DataInterface $configuration
     */
    public function __construct(
        \Magento\Mtf\ObjectManager $objectManager,
        \Magento\Mtf\Config\DataInterface $configuration
    )
    {
        $this->objectManager = $objectManager;
        $this->config = $configuration->get();
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

        /** @var \Magento\Mtf\TestRunner\Rule\Rule $rule */
        $rule = $this->objectManager->create('Magento\Mtf\TestRunner\Rule\Rule');
        $filters = $this->config[$scope];

        foreach ($filters as $type => $class) {
            $filter = $this->objectManager->create($class);
            $rule->addFilter($filter);
        }

        return $rule;
    }
}

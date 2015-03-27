<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\TestRunner\Rule;

use Magento\Mtf\ObjectManager;
use Magento\Mtf\Config\DataInterface;

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
     * @param DataInterface $configuration
     */
    public function __construct(
        \Magento\Mtf\ObjectManager $objectManager,
        DataInterface $configuration
    )
    {
        $this->objectManager = $objectManager;
        $this->config = $configuration->get('rule');
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
        $filters = $this->config[$scope]['filter'];

        foreach ($filters as $type => $data) {
            $filter = $this->objectManager->create($data['class']);
            $rule->addFilter($filter);
        }

        return $rule;
    }
}

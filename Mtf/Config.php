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

namespace Mtf;

/**
 * Class Config
 */
class Config
{
    /**
     * Scenario configuration reader
     *
     * @var \Mtf\Config\Reader
     */
    protected $scenarioConfigReader;

    /**
     * Test Runner configuration reader
     *
     * @var \Mtf\TestRunner\Configuration\Reader
     */
    protected $testRunnerConfigReader;

    /**
     * Test Runner Rule configuration reader
     *
     * @var \Mtf\TestRunner\Rule\Configuration
     */
    protected $testRunnerRuleConfigReader;

    /**
     * Events configuration reader
     *
     * @var \Mtf\System\Event\Config\Reader
     */
    protected $eventsConfigReader;

    /**
     * Default configuration reader
     *
     * @var \Mtf\Configuration\Reader
     */
    protected $defaultConfigReader;

    /**
     * Class Constructor
     *
     * @constructor
     * @param \Mtf\Config\Reader $scenarioConfigReader
     * @param \Mtf\TestRunner\Configuration\Reader $testRunnerConfigReader
     * @param \Mtf\TestRunner\Rule\Configuration $testRunnerRuleConfigReader
     * @param \Mtf\System\Event\Config\Reader $eventsConfigReader
     * @param \Mtf\Configuration\Reader $defaultConfigReader
     */
    public function __construct(
        \Mtf\Config\Reader $scenarioConfigReader,
        \Mtf\TestRunner\Configuration\Reader $testRunnerConfigReader,
        \Mtf\TestRunner\Rule\Configuration $testRunnerRuleConfigReader,
        \Mtf\System\Event\Config\Reader $eventsConfigReader,
        \Mtf\Configuration\Reader $defaultConfigReader
    )
    {
        $this->scenarioConfigReader = $scenarioConfigReader;
        $this->testRunnerConfigReader = $testRunnerConfigReader;
        $this->testRunnerRuleConfigReader = $testRunnerRuleConfigReader;
        $this->eventsConfigReader = $eventsConfigReader;
        $this->defaultConfigReader = $defaultConfigReader;
    }

    /**
     * Get configuration data
     *
     * @param string $configName
     * @param string $scope
     * @return array
     */
    public function getData($configName, $scope = null)
    {
        switch ($configName) {
            case 'scenario':
                return $this->scenarioConfigReader->read($scope);
                break;
            case 'test_runner':
                return $this->testRunnerConfigReader->read($scope);
                break;
            case 'test_runner_rule':
                return $this->testRunnerRuleConfigReader->read($scope);
                break;
            case 'events':
                return $this->eventsConfigReader->read($scope);
                break;
            default:
                $scope = is_null($scope) ? null : 'etc';
                return $this->defaultConfigReader->read($configName, $scope);
        }
    }
}

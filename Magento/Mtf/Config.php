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

namespace Magento\Mtf;

/**
 * Class Config
 */
class Config
{
    /**
     * Scenario configuration reader
     *
     * @var \Magento\Mtf\Config\Reader
     */
    protected $scenarioConfigReader;

    /**
     * Test Runner configuration reader
     *
     * @var \Magento\Mtf\Config\TestRunner
     */
    protected $testRunnerConfigReader;

    /**
     * Test Runner Rule configuration reader
     *
     * @var \Magento\Mtf\Config\TestRunnerRule
     */
    protected $testRunnerRuleConfigReader;

    /**
     * Events configuration reader
     *
     * @var \Magento\Mtf\System\Event\Config\Reader
     */
    protected $eventsConfigReader;

    /**
     * Default configuration reader
     *
     * @var \Magento\Mtf\Config\Reader
     */
    protected $defaultConfigReader;

    /**
     * Class Constructor
     *
     * @constructor
     * @param \Magento\Mtf\ObjectManager $objectManager
     */
    public function __construct(\Magento\Mtf\ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Get configuration data
     *
     * @param string $configName
     * @param string $scope
     * @return array|mixed|null
     */
    public function get($configName = null, $scope = null)
    {
        return $this->getParameter($configName, $scope);
    }

    /**
     * Get configuration data
     *
     * @param string $configName
     * @param string $scope
     * @return array|mixed|null
     */
    public function getParameter($configName = null, $scope = null)
    {
        $parameterPath = null;

        if (strpos($configName, '/')) {
            $parameterPath = $configName;
            $configName = explode('/', $configName)[0];
        }

        switch ($configName) {
            case null:
            case 'application':
            case 'server':
            case 'isolation':
            case 'handler':
                $filePath = is_null($scope) ? null : $scope;
                return  $this->objectManager->get(
                    'Magento\Mtf\Config\GlobalConfig'
                )->get($parameterPath);
                break;
            case 'scenario':
                if (is_null($this->scenarioConfigReader)) {
                    $this->scenarioConfigReader = $this->objectManager->get(
                        'Magento\Mtf\Config\Reader'
                    );
                }
                return $this->scenarioConfigReader->read($scope);
                break;
            case 'test_runner':
                if (is_null($this->testRunnerConfigReader)) {
                    $this->testRunnerConfigReader = $this->objectManager->get(
                        'Magento\Mtf\Config\TestRunner'
                    );
                }
                return $this->testRunnerConfigReader->read($scope);
                break;
            case 'test_runner_rule':
                if (is_null($this->testRunnerRuleConfigReader)) {
                    $this->testRunnerRuleConfigReader = $this->objectManager->get(
                        'Magento\Mtf\Config\TestRunnerRule'
                    );
                }
                return $this->testRunnerRuleConfigReader->read($scope);
                break;
            case 'events':
                if (is_null($this->eventsConfigReader)) {
                    $this->eventsConfigReader = $this->objectManager->get(
                        'Magento\Mtf\System\Event\Config\Reader'
                    );
                }
                return $this->eventsConfigReader->read($scope);
                break;
            default:
                $scope = is_null($scope) ? null : 'etc';
                if (is_null($this->defaultConfigReader)) {
                    $this->defaultConfigReader = $this->objectManager->get(
                        'Magento\Mtf\Config\Reader'
                    );
                }
                return $this->defaultConfigReader->read($configName, $scope);
        }
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

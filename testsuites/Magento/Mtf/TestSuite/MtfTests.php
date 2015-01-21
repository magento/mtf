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

namespace Magento\Mtf\TestSuite;

use Magento\Mtf\ObjectManager;
use Magento\Mtf\ObjectManagerFactory;
use Magento\Mtf\TestRunner\Configuration;

/**
 * Class runner test suite.
 */
class MtfTests extends \PHPUnit_Framework_TestSuite
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \PHPUnit_Framework_TestSuite
     */
    protected $suite;

    /**
     * @var \PHPUnit_Framework_TestResult
     */
    protected $result;

    /**
     * Run collected tests.
     *
     * @param \PHPUnit_Framework_TestResult $result
     * @param bool $filter
     * @param array $groups
     * @param array $excludeGroups
     * @param bool $processIsolation
     *
     * @return \PHPUnit_Framework_TestResult|void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function run(
        \PHPUnit_Framework_TestResult $result = null,
        $filter = false,
        array $groups = [],
        array $excludeGroups = [],
        $processIsolation = false
    ) {
        if ($result === null) {
            $this->result = $this->createResult();
        }
    }

    /**
     * Prepare test suite.
     *
     * @return mixed
     */
    public static function suite()
    {
        $suite = new self();
        return $suite->prepareSuite();
    }

    /**
     * Prepare test suite and apply application state.
     *
     * @return \Magento\Mtf\TestSuite\AppState
     */
    public function prepareSuite()
    {
        $this->init();
        return $this->objectManager->create('Magento\Mtf\TestSuite\AppState');
    }

    /**
     * Call the initialization of ObjectManager.
     */
    public function init()
    {
        $this->initObjectManager();
    }

    /**
     * Initialize ObjectManager.
     *
     * @return void
     */
    private function initObjectManager()
    {
        if (!isset($this->objectManager)) {
            $objectManagerFactory = new ObjectManagerFactory();
            $configurationFileName = isset($_ENV['configuration:Magento/Mtf/TestSuite/MtfTests'])
                ? $_ENV['configuration:Magento/Mtf/TestSuite/MtfTests']
                : 'basic';
            $confFilePath = __DIR__ . '/MtfTests/' . $configurationFileName . '.xml';
            /** @var \Magento\Mtf\TestRunner\Configuration $testRunnerConfiguration */
            $testRunnerConfiguration = $objectManagerFactory->getObjectManager()->get('\Magento\Mtf\TestRunner\Configuration');
            $testRunnerConfiguration->load($confFilePath);
            $testRunnerConfiguration->loadEnvConfig();

            $shared = [
                'Magento\Mtf\TestRunner\Configuration' => $testRunnerConfiguration
            ];
            $this->objectManager = $objectManagerFactory->create($shared);
        }
    }
}

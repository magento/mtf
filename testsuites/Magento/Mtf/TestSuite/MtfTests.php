<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Mtf\TestSuite;

use Mtf\ObjectManagerFactory;
use Mtf\ObjectManager;
use Mtf\TestRunner\Configuration;

/**
 * Class MtfTests
 *
 * @package Magento\Mtf\TestSuite
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
     * Run collected tests
     *
     * @param \PHPUnit_Framework_TestResult $result
     * @param bool $filter
     * @param array $groups
     * @param array $excludeGroups
     * @param bool $processIsolation
     *
     * @return \PHPUnit_Framework_TestResult|void
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
     * Prepare test suite
     *
     * @return mixed
     */
    public static function suite()
    {
        $suite = new self();
        return $suite->prepareSuite();
    }

    /**
     * Prepare test suite and apply application state
     *
     * @return \Mtf\TestSuite\AppState
     */
    public function prepareSuite()
    {
        $this->init();
        return $this->objectManager->create('Mtf\TestSuite\AppState');
    }

    /**
     * Call the initialization of ObjectManager
     */
    public function init()
    {
        $this->initObjectManager();
    }

    /**
     * Initialize ObjectManager
     */
    private function initObjectManager()
    {
        if (!isset($this->objectManager)) {
            $objectManagerFactory = new ObjectManagerFactory();
            $configurationFileName = isset($_ENV['configuration:Magento/Mtf/TestSuite/MtfTests'])
                ? $_ENV['configuration:Magento/Mtf/TestSuite/MtfTests']
                : 'basic';
            $confFilePath = __DIR__ . '/MtfTests/' . $configurationFileName . '.xml';
            $testRunnerConfiguration = new Configuration();
            $testRunnerConfiguration->load($confFilePath);

            $shared = array(
                'Mtf\TestRunner\Configuration' => $testRunnerConfiguration
            );
            $this->objectManager = $objectManagerFactory->create($shared);
        }
    }
}

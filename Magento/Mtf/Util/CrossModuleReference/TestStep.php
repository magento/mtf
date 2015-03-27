<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Util\CrossModuleReference;

/**
 * Class TestStep handles cross module reference of teststep
 */
class TestStep extends Common implements CheckerInterface
{
    const KEY_PROP_SCENARIO = 'scenario';
    const KEY_STEP_MODULE = 'module';

    /**
     * Map of testcases that cross reference test steps in other modules
     *
     * @var array
     */
    protected $testStepCrossModuleMap = null;

    /**
     * @var \Magento\Mtf\Config\DataInterface
     */
    protected $config;

    /**
     * @param \Magento\Mtf\Config\DataInterface $config
     */
    public function __construct(\Magento\Mtf\Config\DataInterface $config)
    {
        $this->config = $config;
    }

    /**
     * For a given module, find all test cases that reference the test steps in the module
     *
     * @param string $moduleName
     * @return array
     */
    public function getCrossModuleReference($moduleName)
    {
        $testCases = [];
        if (!isset($this->testStepCrossModuleMap)) {
            $this->initialize();
        }

        if (empty($this->testStepCrossModuleMap)) {
            return $testCases;
        }

        /** @var $testCaseClass \ReflectionClass */
        foreach ($this->testStepCrossModuleMap as $testCaseClassName => $testStepModules) {
            if (isset($testStepModules[$moduleName])) {
                $testCases[$testCaseClassName] = true;
            }
        }
        return $testCases;
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function initialize()
    {
        $scenarios = $this->config->get(self::KEY_PROP_SCENARIO);
        if (empty($scenarios)) {
            $this->testStepCrossModuleMap = [];
            return;
        }

        $testCases = $this->getTestClassesByType(self::CLASS_TYPE_TESTCASE);
        /** @var $testCaseClass \ReflectionClass */
        foreach ($testCases as $testCaseClassName => $testCaseClass) {
            if (!$testCaseClass->isSubclassOf('\\Magento\Mtf\\TestCase\\Scenario')) {
                continue;
            }
            $testClassShortName = $testCaseClass->getShortName();
            if (!isset($scenarios[$testClassShortName])) {
                continue;
            }

            $config = $scenarios[$testClassShortName];
            $testStepModules = [];
            foreach ($config['step'] as $step) {
                if (!is_array($step)) {
                    continue;
                }
                $stepModule = $step[self::KEY_STEP_MODULE];
                $testStepModules[$stepModule] = true;
            }

            if (!empty($testStepModules)) {
                $this->testStepCrossModuleMap[$testCaseClassName] = $testStepModules;
            }
        }
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Util\CrossModuleReference;

/**
 * Class TestStep handles cross module reference of teststep
 */
class TestStep extends Common implements CheckerInterface
{
    const KEY_PROP_SCENARIO = 'scenario';
    const KEY_METHODS = 'methods';
    const KEY_MODULE = 'module';
    const KEY_STEP_SCENARIO = 'scenario';

    /**
     * Map of testcases that cross reference test steps in other modules
     *
     * @var array
     */
    protected $testStepCrossModuleMap = null;

    /**
     * For a given module, find all test cases that reference the test steps in the module
     *
     * @param string $moduleName
     * @return array
     */
    public function getCrossModuleReference($moduleName)
    {
        $testCases = [];
        if (!isset($this->$testStepCrossModuleMap)) {
            $this->initialize();
        }

        if (empty($this->$testStepCrossModuleMap)) {
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
     */
    protected function initialize()
    {
        $testCases = $this->getTestClassesByType(self::CLASS_TYPE_TESTCASE);
        /** @var $testCaseClass \ReflectionClass */
        foreach ($testCases as $testCaseClassName => $testCaseClass) {
            if (!$testCaseClass->isSubclassOf('\\Mtf\\TestCase\\Scenario')) {
                continue;
            }
            $testStepModules = $this->getTestStepModules($testCaseClass, $testCaseClassName);
            $this->testStepCrossModuleMap[$testCaseClassName] = $testStepModules;
        }
    }

    /**
     * Return external modules whose test steps were used by a given test case
     *
     * @param \ReflectionClass $testCaseClass
     * @param string $testCaseClassName
     * @return array
     */
    protected function getTestStepModules(\ReflectionClass $testCaseClass, $testCaseClassName)
    {
        $testStepModules = [];
        $testCaseModuleName = $this->mapClassNameToModule($testCaseClassName);
        $props = $testCaseClass->getDefaultProperties();
        if (isset($props[self::KEY_PROP_SCENARIO])) {
            foreach ($props[self::KEY_PROP_SCENARIO] as $testClass) {
                foreach ($testClass[self::KEY_METHODS] as $testMethod) {
                    foreach ($testMethod[self::KEY_STEP_SCENARIO] as $step) {
                        $stepModule = $step[self::KEY_MODULE];
                        if ($stepModule != $testCaseModuleName) {
                            $testStepModules[$stepModule] = true;
                        }
                    }
                }
            }
        }
        return $testStepModules;
    }
}

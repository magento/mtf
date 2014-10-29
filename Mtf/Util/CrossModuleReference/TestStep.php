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
    const KEY_STEP_MODULE = 'module';
    const KEY_STEPS = 'steps';
    const KEY_SCENARIO_MODULE = 'module';

    /**
     * Map of testcases that cross reference test steps in other modules
     *
     * @var array
     */
    protected $testStepCrossModuleMap = null;

    /**
     * @var \Mtf\Config\Reader
     */
    protected $scenarioConfigReader;

    /**
     * @param \Mtf\Config\Reader $scenarioConfigReader
     */
    public function __construct(\Mtf\Config\Reader $scenarioConfigReader)
    {
        $this->scenarioConfigReader = $scenarioConfigReader;
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
     */
    protected function initialize()
    {
        $scenarioConfig = $this->scenarioConfigReader->read('etc');
        if (empty($scenarioConfig) || empty($scenarioConfig['scenarios'])) {
            $this->testStepCrossModuleMap = [];
            return;
        }

        $testCases = $this->getTestClassesByType(self::CLASS_TYPE_TESTCASE);
        /** @var $testCaseClass \ReflectionClass */
        foreach ($testCases as $testCaseClassName => $testCaseClass) {
            if (!$testCaseClass->isSubclassOf('\\Mtf\\TestCase\\Scenario')) {
                continue;
            }
            $testClassShortName = $testCaseClass->getShortName();
            if (!isset($scenarioConfig['scenarios'][$testClassShortName])) {
                continue;
            }

            $config = $scenarioConfig['scenarios'][$testClassShortName];
            $testStepModules = [];
            $testCaseModule = $config[self::KEY_SCENARIO_MODULE];
            foreach ($config[self::KEY_METHODS] as $testMethod) {
                foreach ($testMethod[self::KEY_STEPS] as $step) {
                    if (!is_array($step)) {
                        continue;
                    }
                    $stepModule = $step[self::KEY_STEP_MODULE];
                    if ($stepModule != $testCaseModule) {
                        $testStepModules[$stepModule] = true;
                    }
                }
            }

            if (!empty($testStepModules)) {
                $this->testStepCrossModuleMap[$testCaseClassName] = $testStepModules;
            }
        }
    }
}

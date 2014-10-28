<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Util;

use Mtf\ObjectManager;
use Mtf\Util\CrossModuleReference\Common;

/**
 * Class TestClassModuleFilter filters out testcases that are affected by specified modules
 */
class TestClassModuleFilter extends Common
{
    const MODULE_FILTER = 'module_filter';

    /**
     * @var string[]
     */
    protected $moduleFilters;

    /**
     * @var \Mtf\ObjectManager
     */
    protected $objectManager;

    /**
     * List of allowed modules, all testcases in the allowed modules are allowed as well
     *
     * @var string[]
     */
    protected $affectedModules;

    /**
     * List of individual allowed testcases
     *
     * @var string[]
     */
    protected $affectedTestCases;

    /**
     * @constructor
     * @param ObjectManager $objectManager
     */
    public function __construct(
        ObjectManager $objectManager
    ) {
        $this->objectManager = $objectManager;
        $this->init();
    }

    /**
     * Apply module filter to filter out unneeded test cases
     *
     * @param array $testCases
     * @return array
     */
    public function applyFilter($testCases)
    {
        if (empty($this->moduleFilters)) {
            return $testCases;
        }

        foreach ($testCases as $index => $testCase) {
            $module = $this->mapClassNameToModule($testCase['class']);
            $testClassFullName = $testCase['class'];
            if (!strpos('\\', $testClassFullName)) {
                $testClassFullName = '\\' . $testClassFullName;
            }
            if (isset($this->affectedModules[$module])) {
                continue;
            } elseif (isset($this->affectedTestCases[$testClassFullName])) {
                continue;
            } else {
                unset($testCases[$index]);
            }
        }
        return $testCases;
    }

    /**
     * Initialize module filters and related data structures
     *
     * @return void
     */
    protected function init()
    {
        if (empty($_ENV[self::MODULE_FILTER])) {
            $this->moduleFilters = [];
            return;
        }
        if (isset($_ENV[self::MODULE_FILTER])) {
            $this->moduleFilters = array_map('trim', explode(',', $_ENV[self::MODULE_FILTER]));

        } else {
            $this->moduleFilters = [];
        }

        if (empty($this->moduleFilters)) {
            return;
        }

        //
        $this->affectedModules = array_flip($this->moduleFilters);

        /** @var $constraintCrossReference \Mtf\Util\CrossModuleReference\Constraint */
        $constraintCrossReference = $this->objectManager->get('\\Mtf\\Util\\CrossModuleReference\\Constraint');
        /** @var $testStepCrossReference \Mtf\Util\CrossModuleReference\TestStep */
        $testStepCrossReference = $this->objectManager->get('\\Mtf\\Util\\CrossModuleReference\\TestStep');
        /** @var $pageCrossReference \Mtf\Util\CrossModuleReference\Page */
        $pageCrossReference = $this->objectManager->create(
            '\\Mtf\\Util\\CrossModuleReference\\Page',
            [
                'modules' => $this->moduleFilters,
            ]
        );

        $crossModuleReferenceCheckers = [
            $constraintCrossReference,
            $testStepCrossReference,
            $pageCrossReference,
        ];
        $this->affectedTestCases = [];
        foreach ($crossModuleReferenceCheckers as $crossModuleReferenceChecker) {
            foreach ($this->moduleFilters as $module) {
                $affectedTestCases = $crossModuleReferenceChecker->getCrossModuleReference($module);
                $this->affectedTestCases = array_merge($this->affectedTestCases, $affectedTestCases);
            }
        }
    }
}

<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Util\Filter;

use Magento\Mtf\ObjectManager;

/**
 * Class filters out test suites that are affected by specified module.
 */
class TestSuiteModule extends AbstractClassModule
{
    /**
     * Environment field name for allow module list
     */
    const MODULE_FILTER = 'module_filter';

    /**
     * Environment field name for type filtering modules.
     */
    const MODULE_FILTER_STRICT = 'module_filter_strict';

    /**
     * List allow affected test cases.
     *
     * @var array|null
     */
    protected $allowAffectedTestCase = null;

    /**
     * List deny affected test cases.
     *
     * @var array|null
     */
    protected $denyAffectedTestCase = null;

    /**
     * Filters out class.
     *
     * @param string $class
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function apply($class)
    {
        $this->initAffectedTestCase();
        $module = $this->mapClassNameToModule($class);

        if ($this->deny && array_key_exists($module, $this->deny)) {
            return false;
        }
        if ($this->denyAffectedTestCase && in_array($class, $this->denyAffectedTestCase)) {
            return false;
        }

        if (empty($this->allow)) {
            return true;
        }
        if ($this->allow && array_key_exists($module, $this->allow)) {
            return true;
        }
        if ($this->allowAffectedTestCase && in_array($class, $this->allowAffectedTestCase)) {
            return true;
        }

        return false;
    }

    /**
     * Initialize related test cases from modules.
     *
     * @return void
     */
    protected function initAffectedTestCase()
    {
        if (null == $this->allowAffectedTestCase) {

            $this->initEnvironmentCustomization();

            $this->allowAffectedTestCase = [];

            foreach ($this->allow as $module => $strict) {
                if ($strict) {
                    continue;
                }

                $this->allowAffectedTestCase = array_merge(
                    $this->allowAffectedTestCase,
                    $this->getAffectedTestCases($module)
                );
            }
        }

        if (null == $this->denyAffectedTestCase) {
            $this->denyAffectedTestCase = [];

            foreach ($this->deny as $module => $strict) {
                if ($strict) {
                    continue;
                }

                $this->denyAffectedTestCase = array_merge(
                    $this->denyAffectedTestCase,
                    $this->getAffectedTestCases($module)
                );
            }
        }
    }

    /**
     * Override data using environment variables
     * todo Need to be moved to rules configuration xml (including strict options)
     */
    protected function initEnvironmentCustomization()
    {
        $modules = getenv(self::MODULE_FILTER);
        
        if (!empty($modules)) {

            $modules = array_map('trim', explode(',', $modules));

            $strict = getenv(self::MODULE_FILTER_STRICT);
            $strict = (false === $strict) ? 1 : $strict;

            foreach ($modules as $module) {
                $this->allow[$module] = $strict;
            }
        }
    }

    /**
     * Return affected test case.
     *
     * @param string $module
     * @return array
     */
    protected function getAffectedTestCases($module)
    {
        $result = [];

        /** @var $constraintCrossReference \Magento\Mtf\Util\CrossModuleReference\Constraint */
        $constraintCrossReference = ObjectManager::getInstance()->get('\\Magento\Mtf\\Util\\CrossModuleReference\\Constraint');
        /** @var $testStepCrossReference \Magento\Mtf\Util\CrossModuleReference\TestStep */
        $testStepCrossReference = ObjectManager::getInstance()->get('\\Magento\Mtf\\Util\\CrossModuleReference\\TestStep');
        /** @var $pageCrossReference \Magento\Mtf\Util\CrossModuleReference\Page */
        $pageCrossReference = ObjectManager::getInstance()->create(
            '\\Magento\Mtf\\Util\\CrossModuleReference\\Page',
            [
                'constraintChecker' => $constraintCrossReference,
                'modules' => [$module],
            ]
        );

        $crossModuleReferenceCheckers = [
            $constraintCrossReference,
            $testStepCrossReference,
            $pageCrossReference,
        ];
        foreach ($crossModuleReferenceCheckers as $crossModuleReferenceChecker) {
            $affectedTestCases = $crossModuleReferenceChecker->getCrossModuleReference($module);
            $result = array_merge($result, $affectedTestCases);
        }

        return $result;
    }
}

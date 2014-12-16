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

namespace Mtf\Util\Filter;

use Mtf\ObjectManager;

/**
 * Class filters out test suites that are affected by specified module.
 */
class TestSuiteModule extends AbstractClassModule
{
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
     * Return affected test case.
     *
     * @param string $module
     * @return array
     */
    protected function getAffectedTestCases($module)
    {
        $result = [];

        /** @var $constraintCrossReference \Mtf\Util\CrossModuleReference\Constraint */
        $constraintCrossReference = ObjectManager::getInstance()->get('\\Mtf\\Util\\CrossModuleReference\\Constraint');
        /** @var $testStepCrossReference \Mtf\Util\CrossModuleReference\TestStep */
        $testStepCrossReference = ObjectManager::getInstance()->get('\\Mtf\\Util\\CrossModuleReference\\TestStep');
        /** @var $pageCrossReference \Mtf\Util\CrossModuleReference\Page */
        $pageCrossReference = ObjectManager::getInstance()->create(
            '\\Mtf\\Util\\CrossModuleReference\\Page',
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

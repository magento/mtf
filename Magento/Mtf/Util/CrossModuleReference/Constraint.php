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

namespace Magento\Mtf\Util\CrossModuleReference;

use Magento\Mtf\Config\DataInterface;

/**
 * Class Constraint handles cross module reference of constraint
 */
class Constraint extends Common implements CheckerInterface
{
    /**
     * @var \Magento\Mtf\Config\DataInterface
     */
    protected $configVariation;

    /**
     * @var array
     */
    protected $constraintsByModule = null;

    /**
     * @var array
     */
    protected $constraintToTestCasesMap = null;

    /**
     * @constructor
     * @param DataInterface $configVariation
     */
    public function __construct(
        DataInterface $configVariation
    ) {
        $this->configVariation = $configVariation;
    }

    /**
     * Return a list of testcases that uses constraint defined in specified module
     *
     * @param string $moduleName
     * @return array
     */
    public function getCrossModuleReference($moduleName)
    {
        $affectedTestCases = [];

        if (!isset($this->constraintsByModule)) {
            $this->initConstraintConfig();
        }

        if (empty($this->constraintsByModule[$moduleName])) {
            return $affectedTestCases;
        }
        foreach ($this->constraintsByModule[$moduleName] as $constraintName) {
            if (empty($this->constraintToTestCasesMap[$constraintName])) {
                continue;
            }
            foreach ($this->constraintToTestCasesMap[$constraintName] as $testCaseClassName) {
                $testClassModuleName = $this->mapClassNameToModule($testCaseClassName);
                if ($testClassModuleName != $moduleName) {
                    $affectedTestCases[$testCaseClassName] = $testCaseClassName;
                }
            }
        }

        return $affectedTestCases;
    }

    /**
     * Return an array of test cases that uses the specified constraint
     *
     * @param string $constraint
     * @return array
     */
    public function getTestCasesByConstraintReference($constraint)
    {
        if (!isset($this->constraintToTestCasesMap)) {
            $this->initConstraintConfig();
        }

        if (empty($this->constraintToTestCasesMap[$constraint])) {
            return [];
        } else {
            return $this->constraintToTestCasesMap[$constraint];
        }
    }

    /**
     * Initialize constraint configuration
     *
     * @return void
     */
    protected function initConstraintConfig()
    {
        $testCases = $this->configVariation->get('testCase');
        foreach ($testCases as $testCaseClassName => $testCase) {
            foreach ($testCase['variation'] as $variation) {
                if (isset($variation['constraint'])) {
                    foreach ($variation['constraint'] as $constraintClassName => $constraintNavigation) {
                        $constraintModuleName = $this->mapClassNameToModule($constraintClassName);
                        $this->constraintToTestCasesMap[$constraintClassName][$testCaseClassName] = $testCaseClassName;
                        $this->constraintsByModule[$constraintModuleName][$constraintClassName] = $constraintClassName;
                    }
                }
            }
        }
    }
}

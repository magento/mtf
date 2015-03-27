<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Util\CrossModuleReference;

/**
 * Class Page handles cross module page augmentation
 */
class Page extends Common implements CheckerInterface
{
    /**
     * @var array
     */
    protected $modules;

    /**
     * @var array
     */
    protected $affectedTestCasesByModule;

    /**
     * @var Constraint
     */
    protected $constraintChecker;

    /**
     * @constructor
     * @param Constraint $constraintChecker
     * @param array $modules
     */
    public function __construct(
        Constraint $constraintChecker,
        array $modules
    ) {
        $this->constraintChecker = $constraintChecker;
        $this->modules = $modules;
        $this->init();
    }

    /**
     * Initialize cross module page extension
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function init()
    {
        $xmlFiles = $this->getTestXmlsByType(self::XML_TYPE_PAGE);
        foreach ($this->modules as $module) {
            $this->affectedTestCasesByModule[$module] = [];
            $pageClasses = $this->getTestClassesByType(self::CLASS_TYPE_PAGE, $module);
            /** @var $pageClass \ReflectionClass */
            foreach ($pageClasses as $pageClassName => $pageClass) {
                $baseName = $pageClass->getShortName();
                if (!empty($xmlFiles[$baseName])) {
                    foreach ($xmlFiles[$baseName] as $moduleName) {
                        if ($moduleName != $module) {
                            $this->affectedTestCasesByModule[$module] = array_merge(
                                $this->affectedTestCasesByModule[$module],
                                $this->getTestClassesByCrossModulePageReference($moduleName, $module)
                            );
                        }
                    }
                }
            }
        }
    }

    /**
     * Search in $scopeModule for test cases that uses page class in $pageModule
     * Only check classes in TestCase and Constraint
     *
     * @param string $scopeModule
     * @param string $pageModule
     * @return array
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function getTestClassesByCrossModulePageReference($scopeModule, $pageModule)
    {
        $affectedTestCases = [];
        $testCases = $this->getTestClassesByType(self::CLASS_TYPE_TESTCASE, $scopeModule);
        $constraints = $this->getTestClassesByType(self::CLASS_TYPE_CONSTRAINT, $scopeModule);

        $pattern = str_replace('_', '\\', $pageModule) . '\\Test\\Page';
        /** @var $testCaseClass \ReflectionClass */
        foreach ($testCases as $testCaseClassName => $testCaseClass) {
            $testClassFileName = $testCaseClass->getFileName();
            if ($this->matchFileContentIgnoreCase($testClassFileName, $pattern)) {
                $affectedTestCases[$testCaseClassName] = $testCaseClassName;
            }
        }

        /** @var $constraintClass \ReflectionClass */
        foreach ($constraints as $constraintClassName => $constraintClass) {
            $constraintClassFileName = $constraintClass->getFileName();
            if ($this->matchFileContentIgnoreCase($constraintClassFileName, $pattern)) {
                $constraintName = lcfirst($constraintClass->getShortName());
                $testCases = $this->constraintChecker->getTestCasesByConstraintReference($constraintName);
                foreach ($testCases as $testCase) {
                    $moduleName = $this->mapClassNameToModule($testCase);
                    if ($moduleName == $scopeModule) {
                        $affectedTestCases[$testCase] = $testCase;
                    }
                }
            }
        }

        return $affectedTestCases;
    }

    /**
     * Check whether a given file contains specified text
     *
     * @param string $fileName
     * @param string $text
     * @return int
     */
    protected function matchFileContentIgnoreCase($fileName, $text)
    {
        $content = file_get_contents($fileName);
        $pattern = '/.*' . str_replace('\\', '\\\\', $text) . '.*/i';
        return preg_match($pattern, $content);
    }

    /**
     * Find all test cases from modules that extends page definitions in the given module
     *
     * @param string $moduleName
     * @return array
     */
    public function getCrossModuleReference($moduleName)
    {
        if (!empty($this->affectedTestCasesByModule[$moduleName])) {
            return $this->affectedTestCasesByModule[$moduleName];
        }
        return [];
    }
}

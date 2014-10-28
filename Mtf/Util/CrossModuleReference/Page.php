<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Util\CrossModuleReference;

/**
 * Class Page handles cross module page augmentation
 */
class Page extends Common implements CheckerInterface
{
    /**
     * @var array
     */
    protected $modules;

    protected $affectedTestCasesByModule;

    /**
     * @constructor
     * @param array $modules
     */
    public function __construct(
        array $modules
    ) {
        $this->modules = $modules;
        $this->init();
    }

    /**
     * Initialize cross module page extension
     *
     * @return void
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
                                $this->getTestClassesByType(self::CLASS_TYPE_TESTCASE, $moduleName)
                            );
                        }
                    }
                }
            }
        }
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

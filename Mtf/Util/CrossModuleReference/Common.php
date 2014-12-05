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

namespace Mtf\Util\CrossModuleReference;

/**
 * Class Common contains utility functions that can be used by subclasses.
 */
class Common
{
    const CLASS_TYPE_PAGE = 'Page';
    const CLASS_TYPE_TESTCASE = 'TestCase';
    const CLASS_TYPE_CONSTRAINT = 'Constraint';
    const XML_TYPE_PAGE = 'Page';

    /**
     * Map class name to namespace.
     *
     * @param string $className
     * @return string
     */
    protected function mapClassNameToNamespace($className)
    {
        $pieces = explode('\\', $className);

        if (strpos($className, '\\') == 0) {
            return $pieces[1];
        }
        return $pieces[0];
    }

    /**
     * Map class name to module name.
     *
     * @param string $className
     * @return string
     */
    protected function mapClassNameToModule($className)
    {
        $pieces = explode('\\', $className);
        if (strpos($className, '\\') == 0) {
            $moduleName = $pieces[1] . '_' . $pieces[2];
        } else {
            $moduleName = $pieces[0] . '_' . $pieces[1];
        }
        return $moduleName;
    }

    /**
     * Get php classes in tests directory by given type, e.g, TestStep, Page, etc.
     *
     * @param string $type
     * @return array
     */
    protected function getTestClassesByType($type, $moduleName = null)
    {
        $testClasses = [];
        $generatedClassesTopDirectory = MTF_BP . '/generated';
        if ($moduleName) {
            list($modulePrefix, $moduleShortName) = explode('_', $moduleName);
        } else {
            $modulePrefix = '*';
            $moduleShortName = '*';
        }
        $moduleDirectory = '/' . $modulePrefix . '/' . $moduleShortName . '/Test/';
        $directories = glob(MTF_TESTS_PATH . $moduleDirectory . $type);
        $directories = array_merge($directories, glob($generatedClassesTopDirectory . $moduleDirectory . $type));
        foreach ($directories as $directory) {
            $dirIterator = new \RegexIterator(
                new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS)
                ),
                '/.php/i'
            );
            foreach ($dirIterator as $fileInfo) {
                $baseName = $fileInfo->getBasename('.php');
                $path = $fileInfo->getPath();
                if (strpos($path, MTF_TESTS_PATH) !== false) {
                    $nameSpace = str_replace('/', '\\', str_replace(MTF_TESTS_PATH, '', $path));
                } else {
                    $nameSpace = str_replace('/', '\\', str_replace($generatedClassesTopDirectory, '', $path));
                }

                $nameSpace = trim($nameSpace, '\\');
                $className = $nameSpace . '\\' . $baseName;

                $class = new \ReflectionClass($className);
                $testClasses[$className] = $class;
            }
        }

        return $testClasses;
    }

    /**
     * Get specific type of XML files under test directory.
     *
     * @param string $type
     * @return array
     */
    protected function getTestXmlsByType($type)
    {
        $xmlFiles = [];
        $directories = glob(MTF_TESTS_PATH . '/*/*/Test/' . $type);
        foreach ($directories as $directory) {
            $dirIterator = new \RegexIterator(
                new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS)
                ),
                '/.xml/i'
            );
            foreach ($dirIterator as $fileInfo) {
                $baseName = $fileInfo->getBasename('.xml');
                $path = $fileInfo->getPath();
                $nameSpace = str_replace('/', '\\', str_replace(MTF_TESTS_PATH, '', $path));

                $moduleName = $this->mapClassNameToModule($nameSpace);

                $xmlFiles[$baseName][$moduleName] = $moduleName;
            }
        }
        return $xmlFiles;
    }
}

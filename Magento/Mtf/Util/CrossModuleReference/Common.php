<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Util\CrossModuleReference;

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
        end($pieces);
        unset($pieces[key($pieces)]);

        return implode('\\', $pieces);
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
     * @param string|null $moduleName
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
                    new \RecursiveDirectoryIterator(
                        $directory,
                        \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::FOLLOW_SYMLINKS
                    )
                ),
                '/\.php$/i'
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
                    new \RecursiveDirectoryIterator(
                        $directory,
                        \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::FOLLOW_SYMLINKS
                    )
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

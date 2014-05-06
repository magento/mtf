<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Util;

/**
 * Class TestClassResolver
 * @package Mtf\Util
 * @api
 */
class TestClassResolver
{
    /**
     * Collect test classes of the given type from Modules'
     *
     * @param string $classType
     * @return array
     */
    public function get($classType, array $includeOnly = [])
    {
        $classes = [];

        $modules = glob(MTF_TESTS_PATH . '*/*');
        foreach ($modules as $modulePath) {
            if (!is_readable($modulePath . '/Test/' . $classType)) {
                continue;
            }

            $dirIterator = new \RegexIterator(
                new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($modulePath . '/Test/' . $classType, \FilesystemIterator::SKIP_DOTS)
                ),
                '/.php$/i'
            );

            foreach ($dirIterator as $fileInfo) {
                /** @var $fileInfo \SplFileInfo */
                $filePath = $fileInfo->getRealPath();
                $filePath = str_replace('\\', '/', $filePath);
                $classPath = str_replace(MTF_TESTS_PATH, '', $filePath);
                $className = str_replace('/', '\\', $classPath);
                $className = str_replace('.php', '', $className);
                if (!empty($includeOnly)) {
                    if (false === array_search($className, $includeOnly)) {
                        continue;
                    }
                }
                $classes[] = [
                    'name' => $className,
                    'class' => $className,
                    'path' => $filePath
                ];
            }
        }

        return $classes;
    }
}

<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Util;

/**
 * Class TestClassResolver
 *
 * @api
 */
class TestClassResolver
{
    /**
     * @var ModuleResolver
     */
    protected $moduleResolver;

    /**
     * Constructor
     *
     * @param ModuleResolver $moduleResolver
     */
    public function __construct(ModuleResolver $moduleResolver = null)
    {
        if ($moduleResolver) {
            $this->moduleResolver = $moduleResolver;
        } else {
            $this->moduleResolver = ModuleResolver::getInstance();
        }
    }

    /**
     * Collect test classes of the given type from Modules'
     *
     * @param string $classType
     * @param array $includeOnly
     * @return array
     */
    public function get($classType, array $includeOnly = [])
    {
        $classes = [];

        $modules = $this->moduleResolver->getModulesPath();
        foreach ($modules as $modulePath) {
            if (!is_readable($modulePath . '/Test/' . $classType)) {
                continue;
            }

            $dirIterator = new \RegexIterator(
                new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator(
                        $modulePath . '/Test/' . $classType,
                        \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::FOLLOW_SYMLINKS
                    )
                ),
                '/.php$/i'
            );

            foreach ($dirIterator as $fileInfo) {
                /** @var $fileInfo \SplFileInfo */
                $filePath = $fileInfo->getPathname();
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

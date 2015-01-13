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

namespace Mtf\Util;

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

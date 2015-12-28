<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Config\FileResolver;

use Magento\Mtf\Config\FileResolverInterface;
use Magento\Mtf\Util\ModuleResolver;
use Magento\Mtf\Util\Iterator\File;

/**
 * Class Mask
 * @package Magento\Mtf\Config\FileResolver
 */
class Mask implements FileResolverInterface
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
     * Retrieve the list of configuration files with given name that relate to specified scope
     *
     * @param string $filename
     * @param string $scope
     * @return array|\Iterator,\Countable
     */
    public function get($filename, $scope)
    {
        $modulesPath = $this->moduleResolver->getModulesPath();
        $paths = [];

        foreach ($modulesPath as $modulePath) {
            $path = $modulePath . '/Test/' . $scope . '/';
            if (is_readable($path)) {
                $directoryIterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator(
                        $path,
                        \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::FOLLOW_SYMLINKS
                    )
                );
                $regexpIterator = new \RegexIterator($directoryIterator, $filename);
                /** @var \SplFileInfo $file */
                foreach ($regexpIterator as $file) {
                    if ($file->isFile() && $file->isReadable()) {
                        $paths[] = $file->getRealPath();
                    }
                }
            }
        }
        $paths = $this->moduleResolver->sortFilesByModuleSequence($paths);

        $iterator = new File($paths);
        return $iterator;
    }
}

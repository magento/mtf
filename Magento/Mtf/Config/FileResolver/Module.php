<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Config\FileResolver;

use Magento\Mtf\Util\Iterator\File;
use Magento\Mtf\Config\FileResolverInterface;
use Magento\Mtf\Util\ModuleResolver;

/**
 * Provides the list of configuration files collected through modules test folders.
 */
class Module implements FileResolverInterface
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
            $path = $modulePath . '/Test/' . $scope . '/' . $filename;
            if (is_readable($path)) {
                $paths[] = $path;
            }
        }

        $iterator = new File($paths);
        return $iterator;
    }
}

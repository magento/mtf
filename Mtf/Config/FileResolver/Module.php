<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Config\FileResolver;

use Mtf\Util\Iterator\File;
use Magento\Framework\Config\FileResolverInterface;
use Mtf\Util\ModuleResolver;

/**
 * Class Module
 * Provides the list of configuration files collected through modules test folders
 *
 * @internal
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
            $this->moduleResolver = new ModuleResolver();
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

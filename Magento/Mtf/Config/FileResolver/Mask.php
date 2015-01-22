<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Mtf\Config\FileResolver;

use Magento\Mtf\Config\FileResolverInterface;
use Magento\Mtf\Util\ModuleResolver;
use Magento\Mtf\Util\Iterator\File;

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
            $regexpIterator = new \RegexIterator(new \DirectoryIterator($path), $filename);
            /**
             * @var \SplFileInfo $file
             */
            foreach ($regexpIterator as $file) {
                if ($file->isFile() && $file->isReadable()) {
                    $paths[] = $path;
                }
            }
        }

        $iterator = new File($paths);
        return $iterator;
    }
}

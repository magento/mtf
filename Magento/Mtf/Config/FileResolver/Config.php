<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Config\FileResolver;

use Magento\Mtf\Util\Iterator\File;
use Magento\Mtf\Config\FileResolverInterface;

/**
 * Provides the list of MTF global configuration files.
 */
class Config extends Primary implements FileResolverInterface
{
    /**
     * Retrieve the configuration files with given name that relate to MTF global configuration.
     *
     * @param string $filename
     * @param string $scope
     * @return array
     */
    public function get($filename, $scope)
    {
        $distFilename = $filename . '.dist';
        $pathIterator = parent::get($filename, $scope);
        $distPathIterator = parent::get($distFilename, $scope);
        $paths = [];

        foreach ($distPathIterator as $dirFile => $content) {
            $paths[$dirFile] = $dirFile . '/' . $scope .  '/' . $distFilename;
        }
        foreach ($pathIterator as $dirFile => $content) {
            $paths[$dirFile] = $dirFile . '/' . $scope .  '/' . $filename;
        }

        return new File($paths);
    }
}

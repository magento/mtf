<?php
/**
 * Copyright © 2017 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Config\FileResolver;

use Magento\Mtf\Util\Iterator\File;
use Magento\Mtf\Config\FileResolverInterface;

/**
 * Class Primary
 * Provides the list of MTF global configuration files
 *
 * @internal
 */
class Primary implements FileResolverInterface
{
    /**
     * Retrieve the configuration files with given name that relate to MTF global configuration
     *
     * @param string $filename
     * @param string $scope
     * @return array
     */
    public function get($filename, $scope)
    {
        if (!$filename) {
            return [];
        }
        $scope = str_replace('\\', '/', $scope);
        return new File($this->getFilePaths($filename, $scope));
    }

    /**
     * Get list of configuration files
     *
     * @param string $filename
     * @param string $scope
     * @return array
     */
    private function getFilePaths($filename, $scope)
    {
        $paths = [];
        foreach ($this->getPathPatterns($filename, $scope) as $pattern) {
            $paths = array_merge($paths, glob($pattern));
        }
        return array_combine($paths, $paths);
    }

    /**
     * Retrieve patterns for glob function
     *
     * @param string $filename
     * @param string $scope
     * @return array
     */
    private function getPathPatterns($filename, $scope)
    {
        if (substr($scope, 0, strlen(MTF_BP)) === MTF_BP) {
            $patterns = [
                $scope . '/' . $filename,
                $scope . '/*/' . $filename
            ];
        } else {
            $mtfDefaultPath = dirname(dirname(dirname(dirname(__DIR__))));
            $mtfDefaultPath = str_replace('\\', '/', $mtfDefaultPath);
            $patterns = [
                $mtfDefaultPath . '/' . $scope . '/' . $filename,
                $mtfDefaultPath . '/' . $scope . '/*/' . $filename,
                MTF_BP . '/' . $scope . '/' . $filename,
                MTF_BP . '/' . $scope . '/*/' . $filename
            ];
        }
        return str_replace('/', DIRECTORY_SEPARATOR, $patterns);
    }
}

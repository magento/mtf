<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
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

        if (substr($scope, 0, strlen(MTF_BP)) === MTF_BP) {
            $paths[$scope] = $scope . '/' . $filename;
        } else {
            $mtfDefaultPath = dirname(dirname(dirname(dirname(__DIR__))));
            $mtfDefaultPath = str_replace('\\', '/', $mtfDefaultPath);

            $paths[$mtfDefaultPath] = $mtfDefaultPath . '/' . $scope . '/' . $filename;
            $paths[MTF_BP] = MTF_BP . '/' . $scope . '/' . $filename;

            foreach ($paths as $dir => $filename) {
                if (!file_exists($filename)) {
                    unset($paths[$dir]);
                }
            }
        }

        return new File($paths);
    }
}

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
        $mtfDefaultPath = dirname(dirname(dirname(__DIR__)));
        $mtfDefaultPath = str_replace('\\', '/', $mtfDefaultPath);
        $paths[$mtfDefaultPath] = $mtfDefaultPath . '/' . $scope . '/' . $filename;
        $paths[MTF_BP] = MTF_BP . '/' . $scope . '/' . $filename;

        $iterator = new File($paths);
        return $iterator;
    }
}

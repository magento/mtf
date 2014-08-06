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
 * Class Module
 * Provides the list of configuration files collected through modules test folders
 *
 * @internal
 */
class Module implements FileResolverInterface
{
    /**
     * Retrieve the list of configuration files with given name that relate to specified scope
     *
     * @param string $filename
     * @param string $scope
     * @return array|\Iterator,\Countable
     */
    public function get($filename, $scope)
    {
        $paths = glob(MTF_TESTS_PATH . '*/*/Test/etc/' . $scope . '/' . $filename);
        $iterator = new File($paths);
        return $iterator;
    }
}

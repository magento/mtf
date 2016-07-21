<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Repository\Config\FileResolver;

use Magento\Mtf\Config\FileResolver\Mask as MaskFileResolver;
use Magento\Mtf\Config\FileResolverInterface;
use Magento\Mtf\Util\ModuleResolver;

/**
 * Repository file resolver by mask.
 */
class Mask extends MaskFileResolver implements FileResolverInterface
{
    /**
     * Additional files.
     *
     * @var array
     */
    protected $additionalFiles;

    /**
     * @constructor
     * @param ModuleResolver|null $moduleResolver
     * @param array $additionalFiles [optional]
     */
    public function __construct($moduleResolver = null, array $additionalFiles = [])
    {
        parent::__construct($moduleResolver);
        $this->additionalFiles = $additionalFiles;
    }

    /**
     * Get scope of paths.
     *
     * @param string $filename
     * @param string $scope
     * @return array
     */
    public function getPaths($filename, $scope)
    {
        $paths = parent::getPaths($filename, $scope);

        foreach ($this->additionalFiles as $file) {
            $mtfDefaultPath = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
            $mtfDefaultPath = str_replace('\\', '/', $mtfDefaultPath);
            $mtfDefaultPath = str_replace(MTF_BP, '', $mtfDefaultPath);

            $pattern = MTF_BP . '{' . $mtfDefaultPath . ',}' . '/' . $file;
            $paths = array_merge($paths, glob($pattern, GLOB_BRACE));
        }

        return $paths;
    }
}

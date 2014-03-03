<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Interface for minification strategies
 */
namespace Magento\Code\Minifier;

interface StrategyInterface
{
    /**
     * Generates minified file
     *
     * @param string $originalFile path relative to pub/view_cache
     * @param string $targetFile path relative to pub/view_cache
     *
     * @return void
     */
    public function minifyFile($originalFile, $targetFile);
}

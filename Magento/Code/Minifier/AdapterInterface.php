<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Interface for minification adapters
 */
namespace Magento\Code\Minifier;

interface AdapterInterface
{
    /**
     * Minify content
     *
     * @param string $content
     * @return string
     */
    public function minify($content);
}

<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Config;

interface ConverterInterface
{
    /**
     * Convert config
     *
     * @param mixed $source
     * @param array
     */
    public function convert($source);
}

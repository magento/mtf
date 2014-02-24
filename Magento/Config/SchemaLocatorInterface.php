<?php
/**
 * Configuration validation schema locator
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Config;

interface SchemaLocatorInterface
{
    /**
     * Get path to merged config schema
     *
     * @return string|null
     */
    public function getSchema();

    /**
     * Get path to per file validation schema
     *
     * @return string|null
     */
    public function getPerFileSchema();
}

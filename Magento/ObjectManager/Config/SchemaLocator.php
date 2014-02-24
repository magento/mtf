<?php
/**
 * Object manager configuration schema locator
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ObjectManager\Config;

class SchemaLocator implements \Magento\Config\SchemaLocatorInterface
{
    /**
     * Get path to merged config schema
     *
     * @return string
     */
    public function getSchema()
    {
        return realpath(__DIR__ . '/../etc/') . '/config.xsd';
    }

    /**
     * Get path to pre file validation schema
     *
     * @return null
     */
    public function getPerFileSchema()
    {
        return null;
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\ObjectManager\Config;

/**
 * Class SchemaLocator
 * @package Mtf\ObjectManager\Config
 * @internal
 */
class SchemaLocator extends \Magento\ObjectManager\Config\SchemaLocator
{
    /**
     * Get path to merged config schema
     * Schema is updated for MTF needs
     *
     * @return string
     */
    public function getSchema()
    {
        return realpath(__DIR__ . '/../etc/') . DIRECTORY_SEPARATOR . 'config.xsd';
    }
}

<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\ObjectManager\Config;

use Magento\Mtf\Config\SchemaLocatorInterface;

/**
 * Class SchemaLocator
 *
 * @internal
 */
class SchemaLocator implements SchemaLocatorInterface
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

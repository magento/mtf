<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Fixture\InjectableFixture\Replacer;

/**
 * Replacing values schema locator.
 */
class SchemaLocator implements \Magento\Mtf\Config\SchemaLocatorInterface
{
    /**
     * Return path to schema.
     *
     * @return string
     */
    public function getSchema()
    {
        return realpath(__DIR__ . '/../etc') . '/replace.xsd';
    }

    /**
     * Get path to per file validation schema.
     *
     * @return null
     */
    public function getPerFileSchema()
    {
        return null;
    }
}

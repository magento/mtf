<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\Config;

/**
 * Class SchemaLocator
 * Scenario configuration schema locator
 */
class SchemaLocator implements \Magento\Mtf\Config\SchemaLocatorInterface
{
    /**
     * XSD schema path (relatively to MTF path)
     *
     * @var string
     */
    protected $schemaPath;

    /**
     * Class constructor
     *
     * @constructor
     * @param string $schemaPath
     */
    public function __construct($schemaPath)
    {
        if (constant('MTF_BP') && file_exists(MTF_BP . '/' . $schemaPath)) {
            $this->schemaPath =  MTF_BP . '/' . $schemaPath;
        } else {
            $mtfPath = dirname(dirname(dirname(__DIR__)));
            $mtfPath = str_replace('\\', '/', $mtfPath);
            $this->schemaPath =  $mtfPath . '/' . $schemaPath;
        }
    }

    /**
     * Get path to merged config schema
     *
     * @return string
     */
    public function getSchema()
    {
        return $this->schemaPath;
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

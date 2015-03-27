<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Block;

use Magento\Mtf\Util\XmlConverter;

/**
 * Class Mapper
 */
class Mapper
{
    /**
     * XmlConverter instance
     *
     * @var XmlConverter
     */
    protected $xmlConverter;

    /**
     * @constructor
     * @param XmlConverter $xmlConverter
     */
    public function __construct(XmlConverter $xmlConverter)
    {
        $this->xmlConverter = $xmlConverter;
    }

    /**
     * Read .xml file and convert it into array
     *
     * @param string $xmlFilePath
     * @return array
     */
    public function read($xmlFilePath)
    {
        $contentXml = simplexml_load_file($xmlFilePath);
        return $this->xmlConverter->convert($contentXml);
    }
}

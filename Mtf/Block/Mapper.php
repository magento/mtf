<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Block;

use Mtf\Util\XmlConverter;

/**
 * Class Mapper
 *
 * @package Mtf\Block
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

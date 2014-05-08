<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Configuration;

use Mtf\Util\XmlConverter;
use Mtf\Config\FileResolver\Module;

/**
 * Class Reader
 *
 * @package Mtf\Fixture\Configuration
 * @internal
 */
class Reader
{
    /**
     * File Resolver
     *
     * @var Module
     */
    protected $fileResolver;

    /**
     * @var XmlConverter
     */
    protected $xmlConverter;

    /**
     * @constructor
     * @param Module $fileResolver
     * @param \Mtf\Util\XmlConverter $xmlConverter
     */
    public function __construct(Module $fileResolver, XmlConverter $xmlConverter)
    {
        $this->fileResolver = $fileResolver;
        $this->xmlConverter = $xmlConverter;
    }

    /**
     * Read entity files and merge them into one array
     *
     * @param string $entityName
     * @param string $scope
     * @return array
     */
    public function read($entityName, $scope = 'global')
    {
        $result = [];
        $files = $this->fileResolver->get($entityName . '.xml', $scope);
        foreach ($files as $file) {
            $presetXml = simplexml_load_string($file);
            if ($presetXml instanceof \SimpleXMLElement) {
                $array = $this->xmlConverter->convert($presetXml);
                if (is_array($array)) {
                    $result = array_replace($result, $array);
                }
            }
        }

        return $result;
    }
}

<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Configuration;

use Magento\Mtf\Util\XmlConverter;
use Magento\Mtf\Config\FileResolver\Module;

/**
 * Class Reader
 *
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
     * @param \Magento\Mtf\Util\XmlConverter $xmlConverter
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
    public function read($entityName, $scope = 'etc')
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

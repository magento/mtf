<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Configuration;

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
     * @var \Mtf\Config\FileResolver\Module
     */
    protected $fileResolver;

    /**
     * @constructor
     * @param Module $fileResolver
     */
    public function __construct(Module $fileResolver)
    {
        $this->fileResolver = $fileResolver;
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
                $array = $this->convert($presetXml);
                if (is_array($array)) {
                    $result = array_merge($result, $array);
                }
            }
        }

        return $result;
    }

    /**
     * Convert SimpleXMLElement into string
     *
     * @param \SimpleXMLElement $element
     * @return array|string
     */
    public function convert(\SimpleXMLElement $element)
    {
        $result = [];

        foreach ($element->attributes() as $attributeName => $attribute) {
            if ($attribute) {
                $result[$attributeName] = (string)$attribute;
            }
        }

        // add children values
        if ($this->hasChildren($element)) {
            foreach ($element->children() as $childName => $child) {
                $result[$childName] = $this->convert($child);

            }
        } else {
            if (empty($result)) {
                // return as string, if nothing was found
                $result = (string) $element;
            } else {
                // value has zero key element
                $result[0] = (string) $element;
            }
        }
        return $result;
    }

    /**
     * Check whether element has children
     *
     * @param \SimpleXMLElement $element
     * @return bool
     */
    protected function hasChildren(\SimpleXMLElement $element)
    {
        if (!$element->children()) {
            return false;
        }
        // simplexml bug: @attributes is in children() but invisible in foreach
        foreach ($element->children() as $child) {
            return true;
        }
        return false;
    }
}

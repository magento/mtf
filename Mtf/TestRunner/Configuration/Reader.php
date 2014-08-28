<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\TestRunner\Configuration;

use Mtf\Util\XmlConverter;

/**
 * Class Reader
 *
 * @internal
 */
class Reader
{
    /**
     * Read .xml file
     *
     * @param string $configFilePath
     * @return array|string
     */
    public function read($configFilePath)
    {
        $result = [];
        $presetXml = simplexml_load_file($configFilePath);
        if ($presetXml instanceof \SimpleXMLElement) {
            $result = $this->convert($presetXml);
        }

        return $result;
    }

    /**
     * Convert \SimpleXMLElement to array
     *
     * @param \SimpleXMLElement $element
     * @return array|string
     */
    private function convert(\SimpleXMLElement $element)
    {
        $result = [];

        // add children values
        $xmlConverter = new XmlConverter();
        if ($xmlConverter->hasChildren($element)) {
            foreach ($element->children() as $childName => $child) {
                if ($childName === 'suiteRule') {
                    $result['suiteRule'] = $this->convert($child);
                } elseif ($childName === 'objectRule') {
                    $class = (string)$child['for'];
                    $result['objectRules'][$class] = $this->convert($child);
                } elseif ($childName === 'allow') {
                    $result[$childName] = $this->convert($child);
                } elseif ($childName === 'deny') {
                    $result[$childName] = $this->convert($child);
                } else {
                    $result[$childName] = $this->convert($child);
                }
            }
        } else {
            if (empty($result)) {
                // return as string, if nothing was found
                $result = (string)$element;
            } else {
                // value has zero key element
                $result[0] = (string)$element;
            }
        }

        return $result;
    }
}

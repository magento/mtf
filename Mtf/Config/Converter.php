<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Mtf\Config;

/**
 * Class Converter
 * Convert scenario configuration.
 */
class Converter implements \Magento\Framework\Config\ConverterInterface
{
    /**
     * Unique identifier of node.
     */
    const NAME_ATTRIBUTE = 'name';

    /**
     * Module attribute for node.
     */
    const MODULE_ATTRIBUTE = 'module';

    /**
     * Convert xml to array.
     *
     * @param \DOMDocument $source
     * @return array
     */
    public function convert($source)
    {
        $testCases = $source->getElementsByTagName('scenario');
        return ['scenarios' => $this->convertXml($testCases)];
    }

    /**
     * Convert xml node to array or string recursive.
     *
     * @param mixed $elements
     * @return array|string
     */
    protected function convertXml($elements)
    {
        $result = [];

        foreach ($elements as $element) {
            if ($element instanceof \DOMElement) {
                $key = $element->hasAttribute(self::NAME_ATTRIBUTE)
                    ? $element->getAttribute(self::NAME_ATTRIBUTE)
                    : $element->nodeName;
                if ($element->hasChildNodes()) {
                    $result[$key] = $this->convertXml($element->childNodes);
                }
                if ($element->hasAttribute(self::MODULE_ATTRIBUTE)) {
                    $result[$key][self::MODULE_ATTRIBUTE] = $element->getAttribute(self::MODULE_ATTRIBUTE);
                }
            } elseif ($element->nodeType == XML_TEXT_NODE && trim($element->nodeValue) != '') {
                return $element->nodeValue;
            }
        }

        return $result;
    }
}

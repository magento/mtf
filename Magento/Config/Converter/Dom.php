<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Config\Converter;

class Dom implements \Magento\Config\ConverterInterface
{
    const ATTRIBUTES = '__attributes__';
    const CONTENT = '__content__';

    /**
     * Convert dom node tree to array
     *
     * @param $source
     * @return array
     */
    public function convert($source)
    {
        $nodeListData = array();

        /** @var $node \DOMNode */
        foreach ($source->childNodes as $node) {
            if ($node->nodeType == XML_ELEMENT_NODE) {
                $nodeData = array();
                /** @var $attribute \DOMNode */
                foreach ($node->attributes as $attribute) {
                    if ($attribute->nodeType == XML_ATTRIBUTE_NODE) {
                        $nodeData[self::ATTRIBUTES][$attribute->nodeName] = $attribute->nodeValue;
                    }
                }
                $childrenData = $this->convert($node);

                if (is_array($childrenData)) {
                    $nodeData = array_merge($nodeData, $childrenData);
                } else {
                    $nodeData[self::CONTENT] = $childrenData;
                }
                $nodeListData[$node->nodeName][] = $nodeData;
            } elseif ($node->nodeType == XML_CDATA_SECTION_NODE
                || ($node->nodeType == XML_TEXT_NODE && trim($node->nodeValue) != '')
            ) {
                return $node->nodeValue;
            }
        }
        return $nodeListData;
    }
}

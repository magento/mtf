<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Config\Dom\Converter;

class ArrayConverter
{
    const ATTRIBUTES = '__attributes__';
    const CONTENT = '__content__';

    /**
     * Convert dom node tree to array
     *
     * @param \DOMNodeList $input
     * @return array
     */
    public function convert(\DOMNodeList $input)
    {
        $array = array();

        /** @var $item \DOMNode */
        foreach ($input as $item) {
            if ($item->nodeType == XML_ELEMENT_NODE) {
                $arrayElement = array();
                /** @var $attribute \DOMNode */
                foreach ($item->attributes as $attribute) {
                    if ($attribute->nodeType == XML_ATTRIBUTE_NODE) {
                        $arrayElement[self::ATTRIBUTES][$attribute->nodeName] = $attribute->nodeValue;
                    }
                }
                $children = $this->convert($item->childNodes);

                if (is_array($children)) {
                    $arrayElement = array_merge($arrayElement, $children);
                } else {
                    $arrayElement[self::CONTENT] = $children;
                }
                $array[$item->nodeName][] = $arrayElement;
            } elseif ($item->nodeType == XML_CDATA_SECTION_NODE
                || ($item->nodeType == XML_TEXT_NODE && trim($item->nodeValue) != '')
            ) {
                return $item->nodeValue;
            }
        }
        return $array;
    }
}

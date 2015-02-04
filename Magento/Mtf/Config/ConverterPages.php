<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Magento\Mtf\Config;

/**
 * Class Converter.
 *
 * Converts configuration data.
 */
class ConverterPages implements \Magento\Mtf\Config\ConverterInterface
{
    /**
     * Unique identifier of node.
     */
    const NAME_ATTRIBUTE = 'name';

    /**
     * Convert XML to array.
     *
     * @param \DOMDocument $source
     * @return array
     */
    public function convert($source)
    {
        return $this->convertXml(
            $source->documentElement->childNodes
        );
    }

    /**
     * Convert XML node to array or string recursive.
     *
     * @param mixed $elements
     * @return array
     */
    protected function convertXml($elements)
    {
        $result = [];

        foreach ($elements as $element) {
            if ($element instanceof \DOMElement) {
                $elementData = array_merge(
                    $this->getAttributes($element), $this->getChildNodes($element)
                );

                if (!empty($elementData)) {
                    if ($element->hasAttribute(self::NAME_ATTRIBUTE)) {
                        $result[$element->nodeName][$element->getAttribute(self::NAME_ATTRIBUTE)] = $elementData;
                    } else {
                        $result[$element->nodeName][] = $elementData;
                    }
                }
            } elseif ($element->nodeType == XML_TEXT_NODE && trim($element->nodeValue) != '') {
                return ['value' => $element->nodeValue];
            }
        }

        return $result;
    }

    /**
     * Get node attributes.
     *
     * @param \DOMElement $element
     * @return array
     */
    protected function getAttributes(\DOMElement $element)
    {
        $attributes = [];
        if ($element->hasAttributes()) {
            /** @var \DomAttr $attribute */
            foreach ($element->attributes as $attribute) {
                if (trim($attribute->nodeValue) != '' && $attribute->name != self::NAME_ATTRIBUTE) {
                    $attributes[$attribute->nodeName] = $this->castNumeric($attribute->nodeValue);
                }
            }
        }
        return $attributes;
    }

    /**
     * Get child nodes data.
     *
     * @param \DOMElement $element
     * @return array
     */
    protected function getChildNodes(\DOMElement $element)
    {
        $children = [];
        if ($element->hasChildNodes()) {
            $children = $this->convertXml($element->childNodes);
        }
        return $children;
    }

    /**
     * Cast nodeValue to int or double.
     *
     * @param $nodeValue
     * @return float|int
     */
    protected function castNumeric($nodeValue)
    {
        if (is_numeric($nodeValue)) {
            if (preg_match('/^\d+$/', $nodeValue)) {
                $nodeValue = (int) $nodeValue;
            } else {
                $nodeValue = (double) $nodeValue;
            }
        }

        return $nodeValue;
    }
}

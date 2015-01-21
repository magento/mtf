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
 * Class Converter
 * Convert Global configuration.
 */
class ConverterGlobal implements \Magento\Mtf\Config\ConverterInterface
{
    const ACCURACY = 10000;
    /**
     * Unique identifier of node.
     */
    const NAME_ATTRIBUTE = 'name';

    /**
     * Convert xml to array.
     *
     * @param \DOMDocument $source
     * @return array
     */
    public function convert($source)
    {
        return $this->convertXml($source->documentElement->childNodes);
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
                $key = $this->chooseKey($element, $result);
                $result[$key] = [];
                if ($element->hasAttributes()) {
                    foreach ($element->attributes as $attribute) {
                        if (trim($attribute->nodeValue) != '') {
                            $result[$key][$attribute->nodeName] = $this->castNumeric($attribute->nodeValue);
                        }
                    }
                }

                if ($element->hasChildNodes()) {
                    $convert = $this->convertXml($element->childNodes);
                    if (is_array($convert)) {
                        $convert = array_merge($result[$key], $convert);
                    }
                    $result[$key] = $convert;
                }

            } elseif ($element->nodeType == XML_TEXT_NODE && trim($element->nodeValue) != '') {
                return $element->nodeValue;
            }
        }

        return $result;
    }

    /**
     * Cast nodeValue to int or double
     *
     * @param $nodeValue
     * @return float|int
     */
    protected function castNumeric($nodeValue)
    {
        if (is_numeric($nodeValue)) {
            $intNodeValue = (int)$nodeValue * self::ACCURACY;
            $floatNodeValue = $nodeValue * self::ACCURACY;
            if ($floatNodeValue - $intNodeValue > 0) {
                $nodeValue = (double)$nodeValue;
            } else {
                $nodeValue = (int)$nodeValue;
            }
        }

        return $nodeValue;
    }

    /**
     * Choosing key depends on element
     *
     * @param \DOMElement $element
     * @param array $result
     * @return int
     */
    protected function chooseKey(\DOMElement $element, $result)
    {
        $key = $element->hasAttribute(self::NAME_ATTRIBUTE)
            ? $element->getAttribute(self::NAME_ATTRIBUTE)
            : $element->nodeName;
        if ($element->nodeName == 'item' && !$element->hasAttribute(self::NAME_ATTRIBUTE)) {
            $key = count($result);
            return $key;
        }
        return $key;
    }
}

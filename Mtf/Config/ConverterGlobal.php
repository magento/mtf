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
namespace Mtf\Config;

/**
 * Class Converter
 * Convert Global configuration.
 */
class ConverterGlobal implements \Magento\Framework\Config\ConverterInterface
{
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
        return $this->convertXml($source->getElementsByTagName('config'))['config'];
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
                if ($element->nodeName == 'item' && !$element->hasAttribute(self::NAME_ATTRIBUTE)) {
                    $key = count($result);
                }

                $result[$key] = [];

                if ($element->hasAttributes()) {
                    foreach ($element->attributes as $attribute) {
                        if (trim($attribute->nodeValue) != '') {
                            $result[$key][$attribute->nodeName] = $attribute->nodeValue;
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

    private function unify($xmlStructure)
    {
        $result = [];
        if (!is_array($xmlStructure)) {
            return $xmlStructure;
        }

        $iterator = new \RecursiveArrayIterator($xmlStructure);
        foreach ($iterator as $key => $value) {
            if ($key == '@attributes') {
                if (is_array($value)) {
                    $result = array_merge($result, $value);
                }
            } else {
                if ($iterator->hasChildren()) {
                    if ($key == 'item') {
                        $result[] = $this->unify($value);
                    } else {
                        $result[$key] = $this->unify($value);
                    }
                } else {
                    $result[$key] = $value;
                }
            }
        }
        return $result;
    }
}

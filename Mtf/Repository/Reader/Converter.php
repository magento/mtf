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
namespace Mtf\Repository\Reader;

/**
 * Convert repository.
 */
class Converter implements \Magento\Framework\Config\ConverterInterface
{
    /**
     * Unique identifiers of node.
     *
     * @var array
     */
    protected $attributes = ['class', 'name', 'path'];

    /**
     * Attributes in config fields.
     *
     * @var array
     */
    protected $configAttributes = ['path', 'scope', 'scope_id'];

    /**
     * Convert xml to array.
     *
     * @param \DOMDocument $source
     * @return array
     */
    public function convert($source)
    {
        $repositories = $source->getElementsByTagName('storage');
        return $this->convertXml($repositories);
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
                $key = $this->getKey($element);
                if ($element->hasAttribute('path')) {
                    $result['collection'][$key] = $this->convertConfigData($element);
                    $result['collection'][$key]['value'] = $this->convertXml($element->childNodes);
                } elseif ($element->hasChildNodes()) {
                    $result[$key] = $this->convertXml($element->childNodes);
                }
            } elseif ($element->nodeType == XML_TEXT_NODE && trim($element->nodeValue) != '') {
                return $element->nodeValue;
            }
        }

        return $result;
    }

    /**
     * Convert config data.
     *
     * @param \DOMElement $element
     * @return array
     */
    protected function convertConfigData(\DOMElement $element)
    {
        $result = [];
        foreach ($this->configAttributes as $configAttribute) {
            if ($element->hasAttribute($configAttribute)) {
                $result[$configAttribute] = $element->getAttribute($configAttribute);
            }
        }

        return $result;
    }

    /**
     * Get key for current element.
     *
     * @param \DOMElement $element
     * @return string
     */
    protected function getKey(\DOMElement $element)
    {
        $key = $element->nodeName;
        foreach ($this->attributes as $attribute) {
            if ($element->hasAttribute($attribute)) {
                $key = $element->getAttribute($attribute);
            }
        }

        return $key;
    }
}

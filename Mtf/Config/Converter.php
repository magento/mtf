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

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
namespace Mtf\TestCase\Config;

/**
 * Class Converter
 * Convert scenario configuration.
 */
class Converter implements \Mtf\Config\ConverterInterface
{
    /**
     * Node attribute identifier
     */
    const IDENTIFIER = 'name';

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
     * Convert Xml to array
     *
     * @param $elements
     * @return array
     */
    protected function convertXml($elements)
    {
        $result = [];
        foreach ($elements as $element) {
            if ($element instanceof \DOMElement) {
                if ($element->hasChildNodes()) {
                    $result[$element->localName][$element->getAttribute(self::IDENTIFIER)] =
                        $this->convertXml($element->childNodes);
                }
                foreach ($element->attributes as $attribute) {
                    $result[$element->localName][$element->getAttribute(self::IDENTIFIER)][$attribute->name] =
                        $attribute->value;
                }
            }
        }
        return $result;
    }
}

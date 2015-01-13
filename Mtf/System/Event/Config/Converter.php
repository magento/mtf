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

namespace Mtf\System\Event\Config;

use Magento\Framework\Config\ConverterInterface;

class Converter implements ConverterInterface
{
    /**
     * Convert xml node to array or string
     *
     * @param \DOMDocument $node
     * @return array|string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function convert($node)
    {
        $result = [];
        if ($node->nodeType == XML_TEXT_NODE) {
            $result = $node->nodeValue;
        } else {
            if ($node->hasChildNodes()) {
                $children = $node->childNodes;
                for ($i = 0; $i < $children->length; $i++) {
                    $child = $children->item($i);

                    if ($child->nodeName != '#text') {
                        $result[$child->nodeName][] = $this->convert($child);
                    } else {
                        if ($child->nodeName == '#text') {
                            $text = $this->convert($child);

                            if (trim($text) != '') {
                                $result[$child->nodeName] = $this->convert($child);
                            }
                        }
                    }
                }
            }
            if ($node->hasAttributes()) {
                $attributes = $node->attributes;
                if (!is_null($attributes)) {
                    foreach ($attributes as $attribute) {
                        $result[$attribute->name] = $attribute->value;
                    }
                }
            }
        }
        return $result;
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
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

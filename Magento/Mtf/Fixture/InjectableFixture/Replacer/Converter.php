<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Fixture\InjectableFixture\Replacer;

/**
 * Convert replacing values.
 */
class Converter implements \Magento\Mtf\Config\ConverterInterface
{
    /**
     * Convert xml to array.
     *
     * @param \DOMDocument $source
     * @return array
     */
    public function convert(\DOMDocument $source)
    {
        return $this->convertElement($source->documentElement);
    }

    /**
     * Convert element node to array.
     *
     * @param \DOMElement $element
     * @return array
     */
    protected function convertElement(\DOMElement $element)
    {
        $data = ['path' => [], 'replace' => []];
        if ($element->hasChildNodes()) {
            foreach ($element->childNodes as $node) {
                if ($node instanceof \DOMElement) {
                    $value = $this->convertElement($node);
                    $key = $this->getKey($node);
                    if ($key) {
                        $data[$key][$node->getAttribute($key)] = $value;
                    } else {
                        $data[$key][] = $value;
                    }
                }
            }
        } else {
            $data = $element->getAttribute('value');
        }

        return $data;
    }

    /**
     * Get unique identifier of element.
     *
     * @param \DOMElement $node
     * @return string|null
     */
    protected function getKey(\DOMElement $node)
    {
        $attributes = ['path', 'replace'];
        foreach ($attributes as $attribute) {
            if ($node->hasAttribute($attribute)) {
                return $attribute;
            }
        }
        return null;
    }
}

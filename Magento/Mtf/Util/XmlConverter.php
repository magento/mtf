<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Util;

/**
 * Class XmlConverter
 */
class XmlConverter
{
    /**
     * Convert SimpleXMLElement into string
     *
     * @param \SimpleXMLElement $element
     * @return array|string
     */
    public function convert(\SimpleXMLElement $element)
    {
        $result = [];

        foreach ($element->attributes() as $attributeName => $attribute) {
            if ($attribute) {
                $result[$attributeName] = (string)$attribute;
            }
        }

        // add children values
        if ($this->hasChildren($element)) {
            foreach ($element->children() as $childName => $child) {
                $result[$childName] = $this->convert($child);
            }
        } else {
            if (empty($result)) {
                // return as string, if nothing was found
                $result = (string)$element;
            } else {
                // value has zero key element
                $result[0] = (string)$element;
            }
        }

        return $result;
    }

    /**
     * Check whether element has children
     *
     * @param \SimpleXMLElement $element
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function hasChildren(\SimpleXMLElement $element)
    {
        if (!$element->children()) {
            return false;
        }
        // simplexml bug: @attributes is in children() but invisible in foreach
        foreach ($element->children() as $child) {
            return true;
        }

        return false;
    }
}

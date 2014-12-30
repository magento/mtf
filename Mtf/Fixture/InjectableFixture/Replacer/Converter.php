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

namespace Mtf\Fixture\InjectableFixture\Replacer;

/**
 * Convert replacing values.
 */
class Converter implements \Magento\Framework\Config\ConverterInterface
{
    /**
     * Mapping primary attribute of elements.
     *
     * @var array
     */
    protected $mappingKey = [
        'field' => 'path'
    ];

    /**
     * Convert xml to array.
     *
     * @param \DOMDocument $source
     * @return array
     */
    public function convert($source)
    {
        $replace = $source->getElementsByTagName('replace');
        return $this->convertElement($replace->item(0));
    }

    /**
     * Convert element node to array.
     *
     * @param \DOMElement $element
     * @return array
     */
    protected function convertElement(\DOMElement $element)
    {
        if ($element->hasChildNodes()) {
            $data = [];

            foreach ($element->childNodes as $node) {
                if ($node instanceof \DOMElement) {
                    $tag = $node->nodeName;
                    $key = null;
                    $value = $this->convertElement($node);

                    if (isset($this->mappingKey[$tag])) {
                        $attribute = $this->mappingKey[$tag];
                        $key = $node->hasAttribute($attribute) ? $node->getAttribute($attribute) : null;
                    }

                    if ($key) {
                        $data[$key] = $value;
                    } else {
                        $data[] = $value;
                    }
                }
            }
        } else {
            $data = $element->getAttribute('value');
        }

        return $data;
    }
}

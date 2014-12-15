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

namespace Mtf\TestRunner\Rule\Configuration;

/**
 * Convert rule configuration.
 */
class Converter implements \Magento\Framework\Config\ConverterInterface
{
    /**
     * Convert xml to array.
     *
     * @param \DOMDocument $source
     * @return array
     */
    public function convert($source)
    {
        $config = $source->getElementsByTagName('config');
        $convertedNode = $this->convertElement($config->item(0));

        return isset($convertedNode['value']['rule']) ? $convertedNode['value']['rule'] : [];
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
                    $convertMethod = 'convert' . ucfirst($tag);

                    $convertMethod = method_exists($this, $convertMethod) ? $convertMethod : 'convertElement';
                    $convertedNode = $this->$convertMethod($node);

                    $isSingle = $convertedNode['isSingle'];
                    $key = $convertedNode['key'];
                    $value = $convertedNode['value'];

                    if ($key) {
                        $data[$tag][$key] = isset($data[$tag][$key]) ? $data[$tag][$key] : [];
                        $data[$tag][$key] = $isSingle ? $value : array_merge($data[$tag][$key], [$value]);
                    } else {
                        $data[$tag] = isset($data[$tag]) ? $data[$tag] : [];
                        $data[$tag] = $isSingle ? $value : array_merge($data[$tag], [$value]);
                    }
                }
            }
        } else {
            $data = $element->getAttribute('value');
        }

        return [
            'isSingle' => false,
            'key' => null,
            'value' => $data
        ];
    }

    /**
     * Convert "rule" node to array.
     *
     * @param \DOMElement $rule
     * @return array
     */
    protected function convertRule(\DOMElement $rule)
    {
        $data = $this->convertElement($rule);
        $scope = $rule->getAttribute('scope');

        $data['isSingle'] = true;
        $data['key'] = $scope;
        $data['value'] = isset($data['value']['filter']) ? $data['value']['filter'] : [];
        return $data;
    }

    /**
     * Convert "filter" node to array.
     *
     * @param \DOMElement $filter
     * @return array
     */
    protected function convertFilter(\DOMElement $filter)
    {
        return [
            'isSingle' => true,
            'key' => $filter->getAttribute('name'),
            'value' => $filter->getAttribute('class')
        ];
    }
}

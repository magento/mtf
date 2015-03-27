<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\TestRunner\Rule\Config;

/**
 * Convert rule configuration.
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
        $config = $source->getElementsByTagName('config');
        $convertedNode = $this->convertElement($config->item(0));

        return isset($convertedNode['value']['rule']) ? $convertedNode['value']['rule'] : [];
    }

    /**
     * Convert element node to array.
     *
     * @param \DOMElement $element
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
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

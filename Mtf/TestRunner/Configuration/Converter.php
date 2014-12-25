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

namespace Mtf\TestRunner\Configuration;

/**
 * Convert test runner configuration.
 */
class Converter implements \Mtf\Config\ConverterInterface
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

        return $convertedNode['value'];
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
        return $data;
    }

    /**
     * Convert "allow" node to array.
     *
     * @param \DOMElement $allow
     * @return array
     */
    protected function convertAllow(\DOMElement $allow)
    {
        $data = $this->convertElement($allow);

        $data['isSingle'] = true;
        return $data;
    }

    /**
     * Convert "deny" node to array.
     *
     * @param \DOMElement $deny
     * @return array
     */
    protected function convertDeny(\DOMElement $deny)
    {
        $data = $this->convertElement($deny);

        $data['isSingle'] = true;
        return $data;
    }

    /**
     * Convert "module" node to array.
     *
     * @param \DOMElement $module
     * @return array
     */
    protected function convertModule(\DOMElement $module)
    {
        $data = $this->convertElement($module);
        $strict = $module->hasAttribute('strict') ? $module->getAttribute('strict') : 1;

        return [
            'isSingle' => true,
            'key' => $data['value'],
            'value' => $strict
        ];
    }

    /**
     * Convert "tag" node to array.
     *
     * @param \DOMElement $tag
     * @return array
     */
    protected function convertTag(\DOMElement $tag)
    {
        $data = $this->convertElement($tag);
        $group = $tag->getAttribute('group');

        $data['key'] = $group;
        return $data;
    }
}

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
        return $this->convertXml($config->item(0));
    }

    /**
     * Convert xml node to array.
     *
     * @param \DOMElement $config
     * @return array|string
     */
    protected function convertXml(\DOMElement $config)
    {
        $result = [];

        foreach ($config->childNodes as $node) {
            if ($node instanceof \DOMElement) {
                $tag = $node->nodeName;
                $convertMethod = 'convert' . ucfirst($tag);

                if (!isset($result[$tag])) {
                    $result[$tag] = [];
                }
                $result[$tag] += $this->$convertMethod($node);
            }
        }

        return $result;
    }

    /**
     * Convert rule node to array.
     *
     * @param \DOMElement $rule
     * @return array
     */
    function convertRule(\DOMElement $rule)
    {
        $scope = $rule->getAttribute('scope');
        $result = [];

        foreach ($rule->childNodes as $access) {
            if ($access instanceof \DOMElement) {
                foreach ($access->childNodes as $filter) {
                    if ($filter instanceof \DOMElement) {
                        $tag = $filter->nodeName;
                        $group = $filter->hasAttribute('group') ? $filter->getAttribute('group') : null;
                        $value = $filter->getAttribute('value');

                        if ($group) {
                            $result[$access->nodeName][$tag][$group][] = $value;
                        } else {
                            $result[$access->nodeName][$tag][] = $value;
                        }
                    }
                }
            }
        }

        return [$scope => $result];
    }
}

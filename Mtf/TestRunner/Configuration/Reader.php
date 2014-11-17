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

use Mtf\Util\XmlConverter;

/**
 * Class Reader
 *
 * @internal
 */
class Reader
{
    /**
     * Read .xml file
     *
     * @param string $configFilePath
     * @return array|string
     */
    public function read($configFilePath)
    {
        $result = [];
        $presetXml = simplexml_load_file($configFilePath);
        if ($presetXml instanceof \SimpleXMLElement) {
            $result = $this->convert($presetXml);
        }

        return $result;
    }

    /**
     * Convert \SimpleXMLElement to array
     *
     * @param \SimpleXMLElement $element
     * @return array|string
     */
    private function convert(\SimpleXMLElement $element)
    {
        $result = [];

        // add children values
        $xmlConverter = new XmlConverter();
        if ($xmlConverter->hasChildren($element)) {
            foreach ($element->children() as $childName => $child) {
                if ($childName === 'suiteRule') {
                    $result['suiteRule'] = $this->convert($child);
                } elseif ($childName === 'objectRule') {
                    $class = (string)$child['for'];
                    $result['objectRules'][$class] = $this->convert($child);
                } elseif ($childName === 'allow') {
                    $result[$childName] = $this->convert($child);
                } elseif ($childName === 'deny') {
                    $result[$childName] = $this->convert($child);
                } else {
                    $result[$childName] = $this->convert($child);
                }
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
}

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
 * @category    Magento
 * @package     Framework
 * @subpackage  Config
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mtf\Repository\Reader;

/**
 * Mtf configuration XML DOM utility.
 */
class Dom extends \Magento\Framework\Config\Dom
{
    /**
     * Identify node path based on parent path and node attributes.
     *
     * @param \DOMElement $node
     * @param string $parentPath
     * @return string
     */
    protected function _getNodePathByParent(\DOMElement $node, $parentPath)
    {
        $prefix = is_null($this->_rootNamespace) ? '' : self::ROOT_NAMESPACE_PREFIX . ':';
        $path = $parentPath . '/' . $prefix . $node->tagName;
        $idAttribute = $this->_nodeMergingConfig->getIdAttribute($path);

        if ($idAttribute) {
            $attributes = explode("|", $idAttribute);
            foreach ($attributes as $attribute) {
                if ($node->hasAttribute($attribute)) {
                    $value = $node->getAttribute($attribute);
                    $idAttribute = $attribute;
                }
            }
            $path .= isset($value) ? "[@{$idAttribute}='{$value}']" : '';
        }
        return $path;
    }
}

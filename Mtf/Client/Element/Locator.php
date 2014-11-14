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

namespace Mtf\Client\Element;

/**
 * Class Locator
 * Locator class is responsible for keeping selector/location data of element on the page.
 *
 * @api
 */
class Locator implements \ArrayAccess
{
    /**#@+
     * Locator strategy (from Selenium)
     */
    const SELECTOR_XPATH = 'xpath';
    const SELECTOR_CSS = 'css selector';
    const SELECTOR_ID = 'id';
    const SELECTOR_NAME = 'name';
    const SELECTOR_CLASS_NAME = 'class name';
    const SELECTOR_TAG_NAME = 'tag name';
    const SELECTOR_LINK_TEXT = 'link text';
    /**#@-*/

    /**
     * Container for locator properties
     *
     * @var array
     */
    private $container = [];

    /**
     * @constructor
     * @param string $value
     * @param string $strategy
     */
    public function __construct($value, $strategy = self::SELECTOR_CSS)
    {
        $this->container = [
            'value' => $value,
            'using' => $strategy
        ];
    }

    /**
     * Offset to set
     *
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Check whether a offset exists
     *
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * Offset to unset
     *
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * Offset to retrieve
     *
     * @param mixed $offset
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    /**
     * Locator value and strategy string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->container['using'] . '(' . $this->container['value'] . ')';
    }
}

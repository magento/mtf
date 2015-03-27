<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Client;

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

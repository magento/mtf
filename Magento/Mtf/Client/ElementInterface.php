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

namespace Magento\Mtf\Client;

/**
 * Interface Element
 *
 * Classes that implement this interface represents element of a page and provide ability to interact with this element.
 *
 * @api
 */
interface ElementInterface
{
    /**
     * Click
     *
     * @return void
     */
    public function click();

    /**
     * Double click
     *
     * @return void
     */
    public function doubleClick();

    /**
     * Right click
     *
     * @return void
     */
    public function rightClick();

    /**
     * Check whether element is visible
     *
     * @return bool
     */
    public function isVisible();

    /**
     * Check whether element is enabled
     *
     * @return bool
     */
    public function isDisabled();

    /**
     * Check whether element is selected
     *
     * @return bool
     */
    public function isSelected();

    /**
     * Set the value
     *
     * @param string|array $value
     * @return void
     */
    public function setValue($value);

    /**
     * Get the value
     *
     * @return string|array
     */
    public function getValue();

    /**
     * Get content
     *
     * @return string|array
     */
    public function getText();

    /**
     * Find element on the page
     *
     * @param string $selector
     * @param string $strategy [optional]
     * @param string $type = select|multiselect|checkbox|null OR custom class with full namespace
     * @return ElementInterface
     */
    public function find($selector, $strategy = Locator::SELECTOR_CSS, $type = null);

    /**
     * Drag and drop element to(between) another element(s)
     *
     * @param ElementInterface $target
     * @return void
     */
    public function dragAndDrop(ElementInterface $target);

    /**
     * Send a sequence of key strokes to the active element.
     *
     * @param array $keys
     * @return void
     */
    public function keys(array $keys);

    /**
     * Wait until callback isn't null or timeout occurs
     *
     * @param callable $callback
     * @return mixed
     */
    public function waitUntil($callback);

    /**
     * Get all elements by locator
     *
     * @param string $selector
     * @param string $strategy
     * @param null|string $type
     * @return ElementInterface[]
     */
    public function getElements($selector, $strategy = Locator::SELECTOR_CSS, $type = null);

    /**
     * Get absolute selector (for DBG)
     *
     * @return string
     */
    public function getAbsoluteSelector();

    /**
     * Get element locator
     *
     * @return Locator
     */
    public function getLocator();

    /**
     * Get context element
     *
     * @return ElementInterface|null
     */
    public function getContext();

    /**
     * Get the value of a the given attribute of the element
     *
     * @param string $name
     * @return string
     */
    public function getAttribute($name);
}

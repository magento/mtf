<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Client;

use Mtf\Client\Element\Locator;

/**
 * Interface Element
 *
 * Classes that implement this interface represents element of a page and provide ability to interact with this element.
 *
 * @api
 */
interface Element
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
     * Find element by locator in context of current element
     *
     * @param string $selector
     * @param string $strategy [optional]
     * @param string|null $typifiedElement = select|multiselect|null
     * @return Element
     */
    public function find($selector, $strategy = Locator::SELECTOR_CSS, $typifiedElement = null);

    /**
     * Drag and drop element to another element
     *
     * @param Element $target
     * @return void
     */
    public function dragAndDrop(Element $target);

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
     * @param callback $callback
     * @return mixed
     */
    public function waitUntil($callback);

    /**
     * Press OK on an alert, or confirms a dialog
     *
     * @return void
     */
    public function acceptAlert();

    /**
     * Press Cancel on an alert, or does not confirm a dialog
     *
     * @return void
     */
    public function dismissAlert();

    /**
     * Get the alert dialog text
     *
     * @return string
     */
    public function getAlertText();

    /**
     * Set the text to a prompt popup
     *
     * @param string $text
     * @return void
     */
    public function setAlertText($text);

    /**
     * Get current page url
     *
     * @return string
     */
    public function getUrl();

    /**
     * Get all elements by locator
     *
     * @return Element[]
     */
    public function getElements();
}

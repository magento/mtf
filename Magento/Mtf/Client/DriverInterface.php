<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Client;

/**
 * Interface DriverInterface
 */
interface DriverInterface
{
    /**
     * Click
     *
     * @param ElementInterface $element
     * @return void
     */
    public function click(ElementInterface $element);

    /**
     * Double click
     *
     * @param ElementInterface $element
     * @return void
     */
    public function doubleClick(ElementInterface $element);

    /**
     * Right click
     *
     * @param ElementInterface $element
     * @return void
     */
    public function rightClick(ElementInterface $element);

    /**
     * Check whether element is visible
     *
     * @param ElementInterface $element
     * @return bool
     */
    public function isVisible(ElementInterface $element);

    /**
     * Check whether element is enabled
     *
     * @param ElementInterface $element
     * @return bool
     */
    public function isDisabled(ElementInterface $element);

    /**
     * Check whether element is selected
     *
     * @param ElementInterface $element
     * @return bool
     */
    public function isSelected(ElementInterface $element);

    /**
     * Set the value
     *
     * @param ElementInterface $element
     * @param string|array $value
     * @return void
     */
    public function setValue(ElementInterface $element, $value);

    /**
     * Get the value
     *
     * @param ElementInterface $element
     * @return null|string
     */
    public function getValue(ElementInterface $element);

    /**
     * Get content
     *
     * @param ElementInterface $element
     * @return string
     */
    public function getText(ElementInterface $element);

    /**
     * Find element on the page
     *
     * @param string $selector
     * @param string $strategy
     * @param string $type = select|multiselect|checkbox|null OR custom class with full namespace
     * @param ElementInterface $context
     * @return ElementInterface
     */
    public function find(
        $selector,
        $strategy = Locator::SELECTOR_CSS,
        $type = null,
        ElementInterface $context = null
    );

    /**
     * Drag and drop element to(between) another element(s)
     *
     * @param ElementInterface $element
     * @param ElementInterface $target
     * @return void
     */
    public function dragAndDrop(ElementInterface $element, ElementInterface $target);

    /**
     * Send a sequence of key strokes to the active element.
     *
     * @param ElementInterface $element
     * @param array $keys
     * @return void
     */
    public function keys(ElementInterface $element, array $keys);

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
     * @param ElementInterface $context
     * @param string $selector
     * @param string $strategy
     * @param null|string $type
     * @param bool $wait
     * @return ElementInterface[]
     */
    public function getElements(
        ElementInterface $context,
        $selector,
        $strategy = Locator::SELECTOR_CSS,
        $type = null,
        $wait = true
    );

    /**
     * Get the value of a the given attribute of the element
     *
     * @param ElementInterface $element
     * @param string $name
     * @return string
     */
    public function getAttribute(ElementInterface $element, $name);

    /**
     * Open page
     *
     * @param string $url
     * @return void
     */
    public function open($url);

    /**
     * Back to previous page
     * @return void
     */
    public function back();

    /**
     * Forward page
     *
     * @return void
     */
    public function forward();

    /**
     * Refresh page
     *
     * @return void
     */
    public function refresh();

    /**
     * Reopen browser
     *
     * @return void
     */
    public function reopen();

    /**
     * Change the focus to a frame in the page by locator
     *
     * @param Locator $locator
     * @return void
     */
    public function switchToFrame(Locator $locator);

    /**
     * Close the current window
     *
     * @return void
     */
    public function closeWindow();

    /**
     * Select window by its name
     *
     * @return void
     */
    public function selectWindow();

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
     * Get Html page source
     *
     * @return string
     */
    public function getHtmlSource();

    /**
     * Get binary string of image
     *
     * @return string
     */
    public function getScreenshotData();

    /**
     * Inject Js Error collector
     *
     * @return void
     */
    public function injectJsErrorCollector();

    /**
     * Get js errors
     *
     * @return string[]
     */
    public function getJsErrors();

    /**
     * Set focus on element
     *
     * @param ElementInterface $element
     * @return mixed
     */
    public function focus(ElementInterface $element);

    /**
     * Hover mouse over an element.
     *
     * @param ElementInterface $element
     * @return void
     */
    public function hover(ElementInterface $element);
}

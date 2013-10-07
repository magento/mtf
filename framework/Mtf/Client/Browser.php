<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Client;

use Mtf\Client\Element\Locator;

/**
 * Interface Browser
 *
 * Interface provides declaration of methods to perform browser actions such as navigation,
 * working with windows, alerts, prompts etc.
 *
 * @package Mtf\Client
 */
interface Browser
{
    /**
     * Open page
     *
     * @param string $url
     */
    public function open($url);

    /**
     * Back to previous page
     */
    public function back();

    /**
     * Forward page
     */
    public function forward();

    /**
     * Refresh page
     */
    public function refresh();

    /**
     * Reopen browser
     */
    public function reopen();

    /**
     * Change the focus to a frame in the page by locator
     *
     * @param Locator|null $locator
     */
    public function switchToFrame($locator = null);

    /**
     * Close the current window
     */
    public function closeWindow();

    /**
     * Select window by its name
     */
    public function selectWindow();

    /**
     * Find element on the page
     *
     * @param string $selector
     * @param string $strategy [optional]
     * @param string|null $typifiedElement = select|multiselect|null
     * @return Element
     */
    public function find($selector, $strategy = Locator::SELECTOR_CSS, $typifiedElement = null);

    /**
     * Wait until callback isn't null or timeout occurs
     *
     * @param callback $callback
     * @return mixed
     */
    public function waitUntil($callback);

    /**
     * Get current page url
     *
     * @return string
     */
    public function getUrl();
}

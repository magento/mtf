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
 * Interface Browser
 *
 * Interface provides declaration of methods to perform browser actions such as navigation,
 * working with windows, alerts, prompts etc.
 *
 * @api
 */
interface Browser
{
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
     * @param Locator|null $locator
     * @return void
     */
    public function switchToFrame($locator = null);

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
}

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

namespace Mtf\Client;

/**
 * Interface Browser
 *
 * Interface provides declaration of methods to perform browser actions such as navigation,
 * working with windows, alerts, prompts etc.
 *
 * @api
 */
interface BrowserInterface
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
     * @return string
     */
    public function getJsErrors();
}

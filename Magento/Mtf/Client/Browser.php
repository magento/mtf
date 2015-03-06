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

use Magento\Mtf\Config\Data;
use Magento\Mtf\ObjectManager;
use Magento\Mtf\System\Event\EventManagerInterface;

/**
 * Class Browser
 */
final class Browser implements BrowserInterface
{
    /**
     * Remote driver
     *
     * @var DriverInterface
     */
    protected $driver;

    /**
     * Configuration for driver
     *
     * @var Config
     */
    protected $configuration;

    /**
     * Event manager to manage events
     *
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * Application object manager
     *
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Constructor
     *
     * @param DriverInterface $driver
     * @param EventManagerInterface $eventManager
     * @param Data $configuration
     * @param ObjectManager $objectManager
     */
    public function __construct(
        DriverInterface $driver,
        EventManagerInterface $eventManager,
        Data $configuration,
        ObjectManager $objectManager
    ) {
        $this->driver = $driver;
        $this->configuration = $configuration;
        $this->eventManager = $eventManager;
        $this->objectManager = $objectManager;
    }

    /**
     * Open page
     *
     * @param string $url
     * @return void
     */
    public function open($url)
    {
        $this->driver->open($url);
    }

    /**
     * Back to previous page
     * @return void
     */
    public function back()
    {
        $this->driver->back();
    }

    /**
     * Forward page
     *
     * @return void
     */
    public function forward()
    {
        $this->driver->forward();
    }

    /**
     * Refresh page
     *
     * @return void
     */
    public function refresh()
    {
        $this->driver->refresh();
    }

    /**
     * Reopen browser
     *
     * @return void
     */
    public function reopen()
    {
        $this->driver->reopen();
    }

    /**
     * Change the focus to a frame in the page by locator
     *
     * @param Locator|null $locator
     * @return void
     */
    public function switchToFrame(Locator $locator = null)
    {
        $this->driver->switchToFrame($locator);
    }

    /**
     * Close the current window
     *
     * @return void
     */
    public function closeWindow()
    {
        $this->driver->closeWindow();
    }

    /**
     * Select window by its name
     *
     * @return void
     */
    public function selectWindow()
    {
        $this->driver->selectWindow();
    }

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
    ) {
        return $this->driver->find($selector, $strategy, $type, $context);
    }

    /**
     * Wait until callback isn't null or timeout occurs
     *
     * @param callback $callback
     * @return mixed
     */
    public function waitUntil($callback)
    {
        return $this->driver->waitUntil($callback);
    }

    /**
     * Press OK on an alert, or confirms a dialog
     *
     * @return void
     */
    public function acceptAlert()
    {
        $this->driver->acceptAlert();
    }

    /**
     * Press Cancel on an alert, or does not confirm a dialog
     *
     * @return void
     */
    public function dismissAlert()
    {
        $this->driver->dismissAlert();
    }

    /**
     * Get the alert dialog text
     *
     * @return string
     */
    public function getAlertText()
    {
        return $this->driver->getAlertText();
    }

    /**
     * Set the text to a prompt popup
     *
     * @param string $text
     * @return void
     */
    public function setAlertText($text)
    {
        $this->driver->setAlertText($text);
    }

    /**
     * Get current page url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->driver->getUrl();
    }

    /**
     * Get Html page source
     *
     * @return string
     */
    public function getHtmlSource()
    {
        return $this->driver->getHtmlSource();
    }

    /**
     * Get binary string of image
     *
     * @return string
     */
    public function getScreenshotData()
    {
        return $this->driver->getScreenshotData();
    }

    /**
     * Inject Js Error collector
     *
     * @return void
     */
    public function injectJsErrorCollector()
    {
        $this->driver->injectJsErrorCollector();
    }

    /**
     * Get js errors
     *
     * @return string
     */
    public function getJsErrors()
    {
        return $this->driver->getJsErrors();
    }
}

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

namespace Mtf\Client\Driver\Selenium;

use Mtf\Client\Element\Locator;
use Mtf\System\Config;
use Mtf\System\Event\EventManagerInterface;

/**
 * Class Browser
 *
 * Class provides ability to perform browser actions such as navigation,
 * working with windows, alerts, prompts etc.
 *
 * @api
 */
class Browser implements \Mtf\Client\Browser
{
    /**
     * Selenium test case
     *
     * @var TestCase
     */
    protected $_driver;

    /**
     * Selenium test case prototype
     *
     * @var TestCase
     */
    protected $_prototype;

    /**
     * Configuration for driver
     *
     * @var Config
     */
    protected $_configuration;

    /**
     * Event manager to manage events
     *
     * @var \Mtf\System\Event\EventManager
     */
    protected $_eventManager;

    /**
     * Constructor
     *
     * @constructor
     * @param TestCase $driver
     * @param EventManagerInterface $eventManager
     * @param Config $configuration
     */
    public function __construct(TestCase $driver, EventManagerInterface $eventManager, Config $configuration)
    {
        $this->_prototype = clone $driver;
        $this->_driver = $driver;
        $this->_configuration = $configuration;
        $this->_eventManager = $eventManager;

        $this->_init();
    }

    /**
     * Initialize client driver.
     * @return void
     */
    protected function _init()
    {
        $this->_driver = clone $this->_prototype;
        $this->_driver->setBrowserUrl('about:blank');
        $this->_driver->setupSpecificBrowser($this->_configuration->getConfigParam('server/selenium'));
        $this->_driver->prepareSession();

        $this->_driver->currentWindow()->maximize();
        $this->_driver->cookie()->clear();
        $this->_driver->refresh();
    }

    /**
     * Open an URL page
     *
     * @param string $url
     * @return void
     */
    public function open($url)
    {
        $this->_eventManager->dispatchEvent(['open_before'], [__METHOD__, $url]);
        try {
            $this->_driver->url($url);
        } catch (\PHPUnit_Extensions_Selenium2TestCase_WebDriverException $exception) {
            //@todo Workaround for selenium issues https://code.google.com/p/selenium/issues/detail?id=5165
            $this->_eventManager->dispatchEvent(['exception'], [__METHOD__, $url, $exception->getMessage()]);
            $this->_driver->refresh();
            $this->_driver->url($url);
        }
        $this->_eventManager->dispatchEvent(['open_after'], [__METHOD__, $url]);
    }

    /**
     * Back to previous page
     *
     * @return void
     */
    public function back()
    {
        $this->_driver->back();
        $this->_eventManager->dispatchEvent(['back'], [__METHOD__]);
    }

    /**
     * Forward page
     *
     * @return void
     */
    public function forward()
    {
        $this->_driver->forward();
        $this->_eventManager->dispatchEvent(['forward'], [__METHOD__]);
    }

    /**
     * Refresh page
     *
     * @return void
     */
    public function refresh()
    {
        $this->_driver->refresh();
    }

    /**
     * Open new browser
     * This will lead to clean browser session
     *
     * @return void
     */
    public function reopen()
    {
        $this->_eventManager->dispatchEvent(['reopen'], [__METHOD__]);
        $this->_driver->stop();
        $this->_driver->setSessionStrategy('isolated');
        $this->_init();
        if ($sessionStrategy = $this->_configuration->getConfigParam('server/selenium/sessionStrategy')) {
            $this->_driver->setSessionStrategy($sessionStrategy);
        }
    }

    /**
     * Change the focus to a frame in the page by locator.
     * Changes focus to main page if locator is not passed
     *
     * @param Locator|null $locator
     * @return void
     */
    public function switchToFrame($locator = null)
    {
        if ($locator) {
            $this->_eventManager->dispatchEvent(['switch_to_frame'], [(string) $locator]);
            $criteria = new \PHPUnit_Extensions_Selenium2TestCase_ElementCriteria($locator['using']);
            $criteria->value($locator['value']);
            $element = $this->_driver->element($criteria);
        } else {
            $this->_eventManager->dispatchEvent(['switch_to_frame'], ['Switch to main window']);
            $element = null;
        }
        $this->_driver->frame($element);
    }

    /**
     * Close current window and change focus to previous opened window
     *
     * @return void
     */
    public function closeWindow()
    {
        $windowHandles = $this->_driver->windowHandles();
        if (count($windowHandles) > 1) {
            $this->_driver->window(end($windowHandles));
            $this->_driver->closeWindow();
            $this->_driver->window(reset($windowHandles));
        } else {
            $this->_driver->closeWindow();
        }
    }

    /**
     * Select last opened window
     *
     * @return void
     */
    public function selectWindow()
    {
        $windowHandles = $this->_driver->windowHandles();
        $this->_driver->window(end($windowHandles));
    }

    /**
     * Find element on the page
     *
     * @param string $selector
     * @param string $strategy [optional]
     * @param string $typifiedElement = select|multiselect|dropbox|null
     * @return mixed
     */
    public function find($selector, $strategy = Locator::SELECTOR_CSS, $typifiedElement = null)
    {
        $locator = new Locator($selector, $strategy);
        $this->_eventManager->dispatchEvent(['find'], [__METHOD__, (string) $locator]);
        $className = '\Mtf\Client\Driver\Selenium\Element';

        if (null !== $typifiedElement) {
            $typifiedElement = ucfirst(strtolower($typifiedElement));
            if (class_exists($className . '\\' . $typifiedElement . 'Element')) {
                $className .= '\\' . $typifiedElement . 'Element';
            }
        }

        return new $className($this->_driver, $this->_eventManager, $locator);
    }

    /**
     * Wait until callback isn't null or timeout occurs.
     * Callback example: function() use ($element) {$element->isVisible();}
     * Timeout can be defined in configuration
     *
     * @param callback $callback
     * @return mixed
     */
    public function waitUntil($callback)
    {
        return $this->_driver->waitUntil($callback);
    }

    /**
     * Get current page Url
     *
     * @return string|null
     */
    public function getUrl()
    {
        try {
            if ($this->_driver->alertText()) {
                return null;
            }
        } catch (\PHPUnit_Extensions_Selenium2TestCase_WebDriverException $exception) {
            return $this->_driver->url();
        }
        return $this->_driver->url();
    }

    /**
     * Get current page html source
     *
     * @return string
     */
    public function getHtmlSource()
    {
        return $this->_driver->source();
    }

    /**
     * Get binary string of image
     *
     * @return string
     */
    public function getScreenshotData()
    {
        return $this->_driver->currentScreenshot();
    }

    /**
     * Inject Js Error collector
     *
     * @return void
     */
    public function injectJsErrorCollector()
    {
        $this->_driver->execute(
            [
                'script' => 'window.onerror = function(msg, url, line) {
                var errors = {};
                if (localStorage.getItem("errorsHistory")) {
                    errors = JSON.parse(localStorage.getItem("errorsHistory"));
                }
                if (!(window.location.href in errors)) {
                    errors[window.location.href] = [];
                }
                errors[window.location.href].push("error: \'" + msg + "\' " + "file: " + url + " " + "line: " + line);
                localStorage.setItem("errorsHistory", JSON.stringify(errors));
                }',
                'args' => []
            ]
        );
    }

    /**
     * Get js errors
     *
     * @return string[][]
     */
    public function getJsErrors()
    {
        return $this->_driver->execute(
            [
                'script' => 'errors = JSON.parse(localStorage.getItem("errorsHistory"));
                localStorage.removeItem("errorsHistory");
                return errors;',
                'args' => []
            ]
        );
    }
}

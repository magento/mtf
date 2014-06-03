<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
 * @package \Mtf\Client\Driver\Selenium
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
        $this->_driver->url($url);
        $this->_eventManager->dispatchEvent([__METHOD__], [__METHOD__, $url]);
    }

    /**
     * Back to previous page
     * @return void
     */
    public function back()
    {
        $this->_driver->back();
        $this->_eventManager->dispatchEvent([__METHOD__], [__METHOD__]);
    }

    /**
     * Forward page
     * @return void
     */
    public function forward()
    {
        $this->_driver->forward();
        $this->_eventManager->dispatchEvent([__METHOD__], [__METHOD__]);
    }

    /**
     * Refresh page
     * @return void
     */
    public function refresh()
    {
        $this->_driver->refresh();
    }

    /**
     * Open new browser
     * This will lead to clean browser session
     * @return void
     */
    public function reopen()
    {
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
            $criteria = new \PHPUnit_Extensions_Selenium2TestCase_ElementCriteria($locator['using']);
            $criteria->value($locator['value']);
            $element = $this->_driver->element($criteria);
        } else {
            $element = null;
        }
        $this->_driver->frame($element);
    }

    /**
     * Close current window and change focus to previous opened window
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
        $this->_eventManager->dispatchEvent([__METHOD__], [__METHOD__, (string) $locator]);
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
     * @return string
     */
    public function getUrl()
    {
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
     * Inject Js Error collector
     */
    public function injectJsErrorCollector()
    {
        $this->_driver->execute(
            'window.MtfJsErrors = []; window.onerror = function(e) {window.MtfJsErrors[document.Url][] = e;}'
        );
    }

    /**
     * Get js errors
     *
     * @return string[][]
     */
    public function getJsErrors()
    {
        return $this->_driver->execute('return window.MtfJsErrors');
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Mtf\Client\Driver\Selenium;

use Mtf\System\Config;
use Mtf\ObjectManager;
use Mtf\Client\Locator;
use Mtf\Client\DriverInterface;
use Mtf\Client\ElementInterface;
use Mtf\System\Event\EventManagerInterface;

/**
 * Class Driver
 */
class Driver implements DriverInterface
{
    /**
     * Driver configuration
     *
     * @var Config
     */
    protected $configuration;

    /**
     * Selenium test case factory
     *
     * @var RemoteDriverFactory
     */
    protected $remoteDriverFactory;

    /**
     * @var RemoteDriver
     */
    protected $driver;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Constructor
     *
     * @param Config $configuration
     * @param RemoteDriverFactory $remoteDriverFactory
     */
    public function __construct(
        Config $configuration,
        RemoteDriverFactory $remoteDriverFactory,
        EventManagerInterface $eventManager,
        ObjectManager $objectManager
    ) {
        $this->configuration = $configuration;
        $this->remoteDriverFactory = $remoteDriverFactory;
        $this->eventManager = $eventManager;
        $this->objectManager = $objectManager;

        $this->init();
    }

    /**
     * Destructor
     *
     * @return void
     */
    public function __destruct()
    {
        $this->driver->stop();
    }

    /**
     * Initial web driver
     *
     * @return void
     */
    protected function init()
    {
        $this->driver = $this->remoteDriverFactory->crate();

        $this->driver->setBrowserUrl('about:blank');
        $this->driver->setupSpecificBrowser($this->configuration->getConfigParam('server/selenium'));
        $this->driver->prepareSession();
        $this->driver->currentWindow()->maximize();
        $this->driver->cookie()->clear();
        $this->driver->refresh();
    }

    /**
     * Get native element
     *
     * @param Locator $locator
     * @param \PHPUnit_Extensions_Selenium2TestCase_Element $context
     * @param bool $wait
     * @return \PHPUnit_Extensions_Selenium2TestCase_Element
     */
    protected function getElement(
        Locator $locator,
        \PHPUnit_Extensions_Selenium2TestCase_Element $context = null,
        $wait = true
    ) {
        $context = $context === null
            ? $this->driver
            : $context;

        $criteria = $this->getSearchCriteria($locator);

        if ($wait) {
            return $this->waitUntil(
                function () use ($context, $criteria) {
                    $element = $context->element($criteria);
                    return $element->displayed() ? $element : null;
                }
            );
        }

        $driver = $this->driver;
        $this->waitUntil(
            function () use ($driver) {
                $result = $driver->execute(['script' => "return document['readyState']", 'args' => []]);
                return $result === 'complete' || $result === 'uninitialized';
            }
        );

        return $context->element($criteria);
    }

    /**
     * Get context element
     *
     * @param ElementInterface $element
     * @return null|\PHPUnit_Extensions_Selenium2TestCase_Element
     */
    protected function getContext(ElementInterface $element = null)
    {
        $elements = [];
        $contextElement = null;
        if ($element === null) {
            return $contextElement;
        }

        $elements[] = $element;
        while($element = $element->getContext()) {
            $elements[] = $element;
        }

        /** @var ElementInterface $element */
        foreach (array_reverse($elements) as $element) {
            // First call "getElement" with $contextElement equal "null" value
            $contextElement = $this->getElement($element->getLocator(), $contextElement);
        }

        return $contextElement;
    }

    /**
     * Get search criteria
     *
     * @param Locator $locator
     * @return \PHPUnit_Extensions_Selenium2TestCase_ElementCriteria
     */
    public function getSearchCriteria(Locator $locator)
    {
        $criteria = new \PHPUnit_Extensions_Selenium2TestCase_ElementCriteria($locator['using']);
        $criteria->value($locator['value']);

        return $criteria;
    }

    /**
     * Inject Js Error collector
     *
     * @return mixed
     */
    public function injectJsErrorCollector()
    {
        $this->driver->execute(
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
     * @return mixed
     */
    public function getJsErrors()
    {
        return $this->driver->execute(
            [
                'script' => 'errors = JSON.parse(localStorage.getItem("errorsHistory"));
                localStorage.removeItem("errorsHistory");
                return errors;',
                'args' => []
            ]
        );
    }

    /**
     * Click
     *
     * @param ElementInterface $element
     * @return void
     */
    public function click(ElementInterface $element)
    {
        $absoluteSelector = $element->getAbsoluteSelector();
        $this->eventManager->dispatchEvent(['click_before'], [__METHOD__, $absoluteSelector]);

        $this->getElement($element->getLocator(), $this->getContext($element->getContext()))->click();

        $this->eventManager->dispatchEvent(['click_before'], [__METHOD__, $absoluteSelector]);
    }

    /**
     * Double click
     *
     * @param ElementInterface $element
     * @return void
     */
    public function doubleClick(ElementInterface $element)
    {
        $this->eventManager->dispatchEvent(['double_click_before'], [__METHOD__, $element->getAbsoluteSelector()]);

        $wrappedElement = $this->getElement($element->getLocator(), $this->getContext($element->getContext()));
        $this->driver->moveto($wrappedElement);
        $this->driver->doubleclick();
    }

    /**
     * Right click
     *
     * @param ElementInterface $element
     * @return void
     */
    public function rightClick(ElementInterface $element)
    {
        $this->eventManager->dispatchEvent(['right_click_before'], [__METHOD__, $element->getAbsoluteSelector()]);

        $this->driver->moveto($this->getElement($element->getLocator(), $this->getContext($element->getContext())));
        $this->driver->click(\PHPUnit_Extensions_Selenium2TestCase_SessionCommand_Click::RIGHT);
    }

    /**
     * Check whether element is visible
     *
     * @param ElementInterface $element
     * @return bool
     */
    public function isVisible(ElementInterface $element)
    {
        try {
            $this->eventManager->dispatchEvent(['is_visible'], [__METHOD__, $element->getAbsoluteSelector()]);
            $visible = $this->getElement(
                $element->getLocator(),
                $this->getContext($element->getContext()),
                false
            )->displayed();
        } catch (\PHPUnit_Extensions_Selenium2TestCase_WebDriverException $e) {
            $visible = false;
        }

        return $visible;
    }

    /**
     * Check whether element is enabled
     *
     * @param ElementInterface $element
     * @return bool
     */
    public function isDisabled(ElementInterface $element)
    {
        return !$this->getElement($element->getLocator(), $this->getContext($element->getContext()))->enabled();
    }

    /**
     * Check whether element is selected
     *
     * @param ElementInterface $element
     * @return bool
     */
    public function isSelected(ElementInterface $element)
    {
        return $this->getElement($element->getLocator(), $this->getContext($element->getContext()))->selected();
    }

    /**
     * Set the value
     *
     * @param ElementInterface $element
     * @param string|array $value
     * @return void
     */
    public function setValue(ElementInterface $element, $value)
    {
        $wrappedElement = $this->getElement($element->getLocator(), $this->getContext($element->getContext()));
        $wrappedElement->clear();
        $wrappedElement->value($value);
    }

    /**
     * Get the value
     *
     * @param ElementInterface $element
     * @return null|string
     */
    public function getValue(ElementInterface $element)
    {
        return $this->getElement($element->getLocator(), $this->getContext($element->getContext()))->value();
    }

    /**
     * Get content
     *
     * @param ElementInterface $element
     * @return string
     */
    public function getText(ElementInterface $element)
    {
        return $this->getElement($element->getLocator(), $this->getContext($element->getContext()))->text();
    }

    /**
     * Find element on the page
     *
     * @param string $selector
     * @param string $strategy
     * @param string $type = select|multiselect|checkbox|null OR custom class with full namespace
     * @param ElementInterface $context
     * @return ElementInterface
     * @throws \Exception
     */
    public function find(
        $selector,
        $strategy = Locator::SELECTOR_CSS,
        $type = null,
        ElementInterface $context = null
    ) {
        $this->eventManager->dispatchEvent(['find'], [__METHOD__, sprintf('(%s -> %s)', $strategy, $selector)]);

        $locator = $this->objectManager->create(
            'Mtf\Client\Locator',
            [
                'value' => $selector,
                'strategy' => $strategy
            ]
        );

        $className = 'Mtf\Client\ElementInterface';
        if (null !== $type) {
            if (strpos($type, '\\') === false) {
                $type = ucfirst(strtolower($type));
                if (class_exists('Mtf\Client\Element\\' . $type . 'Element')) {
                    $className = 'Mtf\Client\Element\\' . $type . 'Element';
                }
            } else {
                if (!class_exists($type) && !interface_exists($type)) {
                    throw new \Exception('Requested interface or class does not exists!');
                }
                $className = $type;
            }
        }

        return $this->objectManager->create(
            $className,
            [
                'driver' => $this,
                'locator' => $locator,
                'context' => $context
            ]
        );
    }

    /**
     * Drag and drop element to(between) another element(s)
     *
     * @param ElementInterface $element
     * @param ElementInterface $target
     * @return void
     */
    public function dragAndDrop(ElementInterface $element, ElementInterface $target)
    {
        $this->driver->moveto($this->getElement($element->getLocator(), $this->getContext($element->getContext())));
        $this->driver->buttondown();

        $this->driver->moveto($this->getElement($target->getLocator(), $this->getContext($target->getContext())));
        $this->driver->buttonup();
    }

    /**
     * Send a sequence of key strokes to the active element.
     *
     * @param ElementInterface $element
     * @param array $keys
     * @return void
     */
    public function keys(ElementInterface $element, array $keys)
    {
        $wrappedElement = $this->getElement($element->getLocator(), $this->getContext($element->getContext()));
        $wrappedElement->clear();
        $wrappedElement->click();
        foreach ($keys as $key) {
            $this->driver->keys($key);
        }
    }

    /**
     * Wait until callback isn't null or timeout occurs
     *
     * @param callable $callback
     * @return mixed
     * @throws \Exception
     */
    public function waitUntil($callback)
    {
        try {
            return $this->driver->waitUntil($callback);
        } catch (\Exception $e) {
            throw new \Exception(
                sprintf("Error occurred during waiting for an element with message (%s)",$e->getMessage())
            );
        }
    }

    /**
     * Get all elements by locator
     *
     * @param ElementInterface $context
     * @param string $selector
     * @param string $strategy
     * @param null|string $type
     * @return ElementInterface[]
     */
    public function getElements(
        ElementInterface $context,
        $selector,
        $strategy = Locator::SELECTOR_CSS,
        $type = null
    ) {
        $locator = $this->objectManager->create(
            'Mtf\Client\Locator',
            [
                'value' => $selector,
                'strategy' => $strategy
            ]
        );
        $resultElements = [];
        $elements = $this->getElement($context->getLocator(), $this->getContext($context->getContext()))
            ->elements($this->getSearchCriteria($locator));
        for ($length = count($elements), $i = 0; $i < $length; ++$i) {
            $resultElements[$i] = $this->find($selector, $strategy, $type, $context);
        }

        return $resultElements;
    }

    /**
     * Get the value of a the given attribute of the element
     *
     * @param ElementInterface $element
     * @param string $name
     * @return string
     */
    public function getAttribute(ElementInterface $element, $name)
    {
        return $this->getElement($element->getLocator(), $this->getContext($element->getContext()))
            ->attribute($name);
    }

    /**
     * Open page
     *
     * @param string $url
     * @return void
     */
    public function open($url)
    {
        $this->eventManager->dispatchEvent(['open_before'], [__METHOD__, $url]);
        $this->driver->url($url);
        $this->eventManager->dispatchEvent(['open_after'], [__METHOD__, $url]);
    }

    /**
     * Back to previous page
     * @return void
     */
    public function back()
    {
        $this->driver->back();
        $this->eventManager->dispatchEvent(['back'], [__METHOD__]);
    }

    /**
     * Forward page
     *
     * @return void
     */
    public function forward()
    {
        $this->driver->forward();
        $this->eventManager->dispatchEvent(['forward'], [__METHOD__]);
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
        $this->eventManager->dispatchEvent(['reopen'], [__METHOD__]);
        $this->driver->stop();
        if ($sessionStrategy = $this->configuration->getConfigParam('server/selenium/sessionStrategy')) {
            $this->driver->setSessionStrategy($sessionStrategy);
        } else {
            $this->driver->setSessionStrategy('isolated');
        }
        $this->init();
    }

    /**
     * Change the focus to a frame in the page by locator
     *
     * @param Locator|null $locator
     * @return void
     */
    public function switchToFrame(Locator $locator = null)
    {
        if ($locator) {
            $this->eventManager->dispatchEvent(['switch_to_frame'], [(string) $locator]);
            $element = $this->getElement($locator);
        } else {
            $this->eventManager->dispatchEvent(['switch_to_frame'], ['Switch to main window']);
            $element = null;
        }
        $this->driver->frame($element);
    }

    /**
     * Close the current window
     *
     * @return void
     */
    public function closeWindow()
    {
        $windowHandles = $this->driver->windowHandles();
        if (count($windowHandles) > 1) {
            $this->driver->window(end($windowHandles));
            $this->driver->closeWindow();
            $this->driver->window(reset($windowHandles));
        } else {
            $this->driver->closeWindow();
        }
    }

    /**
     * Select window by its name
     *
     * @return void
     */
    public function selectWindow()
    {
        $windowHandles = $this->driver->windowHandles();
        $this->driver->window(end($windowHandles));
    }

    /**
     * Press OK on an alert, or confirms a dialog
     *
     * @return void
     */
    public function acceptAlert()
    {
        $this->driver->acceptAlert();
        $this->eventManager->dispatchEvent(['accept_alert_after'], [__METHOD__]);
    }

    /**
     * Press Cancel on an alert, or does not confirm a dialog
     *
     * @return void
     */
    public function dismissAlert()
    {
        $this->driver->dismissAlert();
        $this->eventManager->dispatchEvent(['dismiss_alert_after'], [__METHOD__]);
    }

    /**
     * Get the alert dialog text
     *
     * @return string
     */
    public function getAlertText()
    {
        return $this->driver->alertText();
    }

    /**
     * Set the text to a prompt popup
     *
     * @param string $text
     * @return void
     */
    public function setAlertText($text)
    {
        $this->driver->alertText($text);
    }

    /**
     * Get current page url
     *
     * @return string
     */
    public function getUrl()
    {
        try {
            if ($this->driver->alertText()) {
                return null;
            }
        } catch (\PHPUnit_Extensions_Selenium2TestCase_WebDriverException $exception) {
            return $this->driver->url();
        }

        return $this->driver->url();
    }

    /**
     * Get Html page source
     *
     * @return string
     */
    public function getHtmlSource()
    {
        return $this->driver->source();
    }

    /**
     * Get binary string of image
     *
     * @return string
     */
    public function getScreenshotData()
    {
        return $this->driver->currentScreenshot();
    }
}

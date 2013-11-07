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

namespace Mtf\Client\Driver\Selenium;

use Mtf\Client\Element as ElementInterface;
use Mtf\Client\Element\Locator;
use Mtf\Client\Driver\Selenium\SelectElement;

/**
 * Class Element
 *
 * Class provides ability to work with page element
 * (Such as setting/getting value, clicking, drag-n-drop element, etc)
 *
 * @package Mtf\Client\Driver\Selenium\Element
 */
class Element implements ElementInterface
{
    /**
     * PHPUnit Selenium test case
     *
     * @var TestCase;
     */
    protected $_driver;

    /**
     * Element locator
     *
     * @var Locator
     */
    protected $_locator;

    /**
     * Context element
     *
     * @var Element
     */
    protected $_context;

    /**
     * Selenium element
     *
     * @var \PHPUnit_Extensions_Selenium2TestCase_Element
     */
    protected $_wrappedElement;

    /**
     * Initialization.
     * Set driver, context and locator.
     *
     * @constructor
     * @param \PHPUnit_Extensions_Selenium2TestCase $driver
     * @param Locator $locator
     * @param Element $context
     */
    final public function __construct(
        \PHPUnit_Extensions_Selenium2TestCase $driver,
        Locator $locator,
        Element $context = null
    ) {
        $this->_driver = $driver;

        $this->_context = $context;

        $this->_locator = $locator;
    }

    /**
     * Unset wrapped element
     */
    public function __clone()
    {
        $this->_wrappedElement = null;
    }

    /**
     * Return Wrapped Element.
     * If element was not created before:
     * 1. Context is defined. If context was not passed to constructor - test case (all page) is taken as context
     * 2. Attempt to get selenium element is performed in loop
     * that is terminated if element is found or after timeout set in configuration
     *
     * @param bool $waitForElementPresent
     * @throws \PHPUnit_Extensions_Selenium2TestCase_Exception|\PHPUnit_Extensions_Selenium2TestCase_WebDriverException
     * @return \PHPUnit_Extensions_Selenium2TestCase_Element
     */
    protected function _getWrappedElement($waitForElementPresent = true)
    {
        if (!$this->_wrappedElement) {
            $context = !empty($this->_context)
                ? $this->_context->_getWrappedElement($waitForElementPresent) : $this->_driver;
            $criteria = new \PHPUnit_Extensions_Selenium2TestCase_ElementCriteria($this->_locator['using']);
            $criteria->value($this->_locator['value']);
            if ($waitForElementPresent) {
                $this->_wrappedElement = $this->_driver->waitUntil(
                    function () use ($context, $criteria) {
                        return $context->element($criteria);
                    }
                );
            } else {
                $driver = $this->_driver;
                $this->_driver->waitUntil( function () use ($driver) {
                    $result = $driver->execute(
                        array('script' => "return document['readyState']", 'args' => array())
                    );
                    return $result === 'complete' || $result === 'uninitialized';
                });
                $this->_wrappedElement = $context->element($criteria);
            }
        }
        return $this->_wrappedElement;
    }

    /**
     * Click at the current element
     */
    public function click()
    {
        $this->_driver->moveto($this->_getWrappedElement());
        $this->_driver->click();
    }

    /**
     * Double-clicks at the current element
     */
    public function doubleClick()
    {
        $this->_driver->moveto($this->_getWrappedElement());
        $this->_driver->doubleclick();
    }

    /**
     * Right-clicks at the current element
     */
    public function rightClick()
    {
        $this->_driver->moveto($this->_getWrappedElement());
        $this->_driver->click(\PHPUnit_Extensions_Selenium2TestCase_SessionCommand_Click::RIGHT);
    }

    /**
     * Check whether element is visible.
     * Return false if element cannot be found
     */
    public function isVisible()
    {
        try {
            $visible = $this->_getWrappedElement(false)->displayed();
        } catch (\PHPUnit_Extensions_Selenium2TestCase_WebDriverException $e) {
            $visible = false;
        }
        return $visible;
    }

    /**
     * Check whether element is enabled
     *
     * @return bool
     */
    public function isDisabled()
    {
        return !$this->_getWrappedElement(false)->enabled();
    }

    /**
     * Check whether element is selected
     *
     * @return bool
     */
    public function isSelected()
    {
        return $this->_getWrappedElement(false)->selected();
    }

    /**
     * Clear and set new value to an element
     *
     * @param string|array $value
     */
    public function setValue($value)
    {
        $this->_getWrappedElement()->clear();
        $this->_getWrappedElement()->value($value);
    }

    /**
     * Get the value of form element
     *
     * @return string
     */
    public function getValue()
    {
        return $this->_getWrappedElement()->value();
    }

    /**
     * Get content of the element
     *
     * @return string
     */
    public function getText()
    {
        return $this->_getWrappedElement()->text();
    }

    /**
     * Find element on the page
     *
     * @param string $selector
     * @param string $strategy [optional]
     * @param string $typifiedElement = select|multiselect|checkbox|null
     * @return mixed
     */
    public function find($selector, $strategy = Locator::SELECTOR_CSS, $typifiedElement = null)
    {
        $locator = new Locator($selector, $strategy);
        $className = '\Mtf\Client\Driver\Selenium\Element';

        if (null !== $typifiedElement) {
            $typifiedElement = ucfirst(strtolower($typifiedElement));
            if (class_exists($className . '\\' . $typifiedElement . 'Element')) {
                $className .= '\\' . $typifiedElement . 'Element';
            }
        }

        return new $className($this->_driver, $locator, $this);
    }

    /**
     * Drag and drop element to(between) another element(s)
     *
     * @param ElementInterface $target
     */
    public function dragAndDrop(ElementInterface $target)
    {
        $this->_driver->moveto($this->_getWrappedElement());
        $this->_driver->buttondown();

        /** @var $target Element */
        $this->_driver->moveto($target->_getWrappedElement());
        $this->_driver->buttonup();
    }

    /**
     * Send a sequence of key strokes to the active element.
     *
     * @param array $keys
     */
    public function keys(array $keys)
    {
        $this->_getWrappedElement()->value(array('value' => $keys));
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
     * Get the alert dialog text
     *
     * @return string
     */
    public function getAlertText()
    {
        return $this->_driver->alertText();
    }

    /**
     * Set the text to the prompt popup
     *
     * @param $text
     */
    public function setAlertText($text)
    {
        $this->_driver->alertText($text);
    }

    /**
     * Press OK on an alert or confirm a dialog
     */
    public function acceptAlert()
    {
        $this->_driver->acceptAlert();
    }

    /**
     * Press Cancel on alert or does not confirm a dialog
     */
    public function dismissAlert()
    {
        $this->_driver->dismissAlert();
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
}

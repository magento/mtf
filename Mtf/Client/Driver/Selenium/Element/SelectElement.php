<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Client\Driver\Selenium\Element;

use Mtf\Client\Element as ElementInterface;
use Mtf\Client\Driver\Selenium\Element;
use Mtf\Client\Element\Locator;

/**
 * Class SelectElement
 * Typified element class for Select elements
 *
 * @package Mtf\Client\Element
 * @api
 */
class SelectElement extends Element
{
    /**
     * Return Wrapped Element.
     * If element was not created before:
     * 1. Context is defined. If context was not passed to constructor - test case (all page) is taken as context
     * 2. Attempt to get selenium element is performed in loop
     * that is terminated if element is found or after timeout set in configuration
     *
     * @param bool $waitForElementPresent
     * @return \PHPUnit_Extensions_Selenium2TestCase_Element_Select
     * @throws \PHPUnit_Extensions_Selenium2TestCase_WebDriverException
     */
    protected function _getWrappedElement($waitForElementPresent = true)
    {
        return $this->_driver->select(parent::_getWrappedElement($waitForElementPresent));
    }

    /**
     * Set the value
     *
     * @param string|array $value
     */
    public function setValue($value)
    {
        $criteria = new \PHPUnit_Extensions_Selenium2TestCase_ElementCriteria('xpath');
        $criteria->value('.//option[contains(text(), "' . $value . '")]');
        $this->_getWrappedElement()->selectOptionByCriteria($criteria);
    }

    /**
     * Select value in dropdown which has option groups
     *
     * @param string $optionGroup
     * @param string $value
     */
    public function setOptionGroupValue($optionGroup, $value)
    {
        $optionLocator = ".//optgroup[@label='$optionGroup']/option[contains(text(), '$value')]";
        $criteria = new \PHPUnit_Extensions_Selenium2TestCase_ElementCriteria('xpath');
        $criteria->value($optionLocator);
        $this->_getWrappedElement(true)->selectOptionByCriteria($criteria);
    }

    /**
     * Get value of the selected option of the element
     *
     * @return string
     */
    public function getValue()
    {
        return $this->_getWrappedElement(true)->selectedLabel();
    }

    /**
     * Get label of the selected option of the element
     *
     * @return string
     */
    public function getText()
    {
        return $this->_getWrappedElement(true)->selectedLabel();
    }

    /**
     * Drag'n'drop method is not accessible in this class.
     * Throws exception if used.
     *
     * @param ElementInterface $target
     * @throws \BadMethodCallException
     */
    public function dragAndDrop(ElementInterface $target)
    {
        throw new \BadMethodCallException('Not applicable for this class of elements (SelectElement)');
    }

    /**
     * Send a sequence of key strokes to the active element.
     *
     * @param array $keys
     */
    public function keys(array $keys)
    {
        $mSelect = $this->_getWrappedElement();
        $criteria = new \PHPUnit_Extensions_Selenium2TestCase_ElementCriteria(Locator::SELECTOR_TAG_NAME);
        $criteria->value('option');
        $mSelect->clearSelectedOptions();
        $options = $mSelect->elements($criteria);
        $pattern = '/^' . implode('', $keys) . '[a-z0-9A-Z-]*/';
        foreach ($options as $option) {
            preg_match($pattern, $option->text(), $matches);
            if ($matches) {
                $this->setValue($option->text());
                break;
            }
        }
    }
}

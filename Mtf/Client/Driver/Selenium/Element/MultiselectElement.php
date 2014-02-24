<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Client\Driver\Selenium\Element;

/**
 * Class MultiselectElement
 * Class provides ability to work with page element multiselect
 * (Such as setting/getting value, clicking, drag-n-drop element, etc)
 *
 * @package Mtf\Client\Driver\Selenium\Element
 * @api
 */
class MultiselectElement extends SelectElement
{
    /**
     * @var \PHPUnit_Extensions_Selenium2TestCase_Element_Select
     */
    protected $_wrappedElement;

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
     * Get value of the selected option of the element
     *
     * @return array
     */
    public function getValue()
    {
        return $this->_getWrappedElement()->selectedValues();
    }

    /**
     * Get Selected Options Labels
     *
     * @return array
     */
    public function getText()
    {
        return $this->_getWrappedElement()->selectedLabels();
    }

    /**
     * Select Options by Label in Multiple Select
     *
     * @param string|array $values
     */
    public function setValue($values)
    {
        $this->clearSelectedOptions();
        if (is_array($values)) {
            foreach ($values as $value) {
                $this->_getWrappedElement()->selectOptionByLabel($value);
            }
        } else {
            $this->_getWrappedElement()->selectOptionByLabel($values);
        }
    }

    /**
     * Clear selected options in multiple select
     */
    public function clearSelectedOptions()
    {
        $this->_getWrappedElement()->clearSelectedOptions();
    }
}

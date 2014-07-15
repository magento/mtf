<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Client\Driver\Selenium\Element;

/**
 * Class StrictselectElement
 * Class provides an ability to work with page element select which select strict values
 *
 * @api
 */
class StrictselectElement extends SelectElement
{
    /**
     * Set the value
     *
     * @param string|array $value
     * @return void
     */
    public function setValue($value)
    {
        $this->_eventManager->dispatchEvent(['set_value'], [__METHOD__, $this->getAbsoluteSelector()]);
        $this->_getWrappedElement()->selectOptionByLabel($value);
    }

    /**
     * Select value in dropdown which has option groups
     *
     * @param string $optionGroup
     * @param string $value
     * @return void
     */
    public function setOptionGroupValue($optionGroup, $value)
    {
        $optionLocator = ".//optgroup[@label='$optionGroup']/option[.=, '$value']";
        $criteria = new \PHPUnit_Extensions_Selenium2TestCase_ElementCriteria('xpath');
        $criteria->value($optionLocator);
        $this->_getWrappedElement(true)->selectOptionByCriteria($criteria);
    }
}

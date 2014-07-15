<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Client\Driver\Selenium\Element;

/**
 * Class StrictmultiselectElement
 * Class provides an ability to work with page element multiselect which select strict values
 *
 * @api
 */
class StrictmultiselectElement extends MultiselectElement
{
    /**
     * Select Options by Label in Multiple Select
     *
     * @param string|array $values
     * @return void
     */
    public function setValue($values)
    {
        $this->_eventManager->dispatchEvent(['set_value'], [__METHOD__, $this->getAbsoluteSelector()]);
        $this->clearSelectedOptions();
        $values = is_array($values) ? $values : [$values];
        foreach ($values as $label) {
            $this->_getWrappedElement()->selectOptionByLabel($label);
        }
    }
}

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

use Mtf\Client\Driver\Selenium\Element;
use Mtf\Client\Element\Locator;

/**
 * Class CheckboxElement
 * Class provides ability to work with page element checkbox
 * (Such as setting/getting value)
 *
 * @package Mtf\Client\Driver\Selenium\Element
 */
class CheckboxElement extends Element
{
    /**
     * Get value of the selected option of the element
     *
     * @return bool
     */
    public function getValue()
    {
        return $this->isSelected() ? true : false;
    }

    /**
     * Mark checkbox if value 'Yes', otherwise unmark
     *
     * @param string $value
     */
    public function setValue($value)
    {
        if (($this->isSelected() && $value == 'No') || (!$this->isSelected() && $value == 'Yes')) {
            $this->click();
        }
    }
}

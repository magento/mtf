<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Client\Element;

/**
 * Class CheckboxElement
 * Class provides ability to work with page element checkbox
 * (Such as setting/getting value)
 *
 * @api
 */
class CheckboxElement extends SimpleElement
{
    /**
     * Get value of the selected option of the element
     *
     * @return string
     */
    public function getValue()
    {
        $this->eventManager->dispatchEvent(['get_value'], [$this->getAbsoluteSelector()]);
        return $this->isSelected() ? 'Yes' : 'No';
    }

    /**
     * Mark checkbox if value 'Yes', otherwise unmark
     *
     * @param string $value
     * @return void
     */
    public function setValue($value)
    {
        $this->eventManager->dispatchEvent(['set_value'], [__METHOD__, $this->getAbsoluteSelector()]);
        if (($this->isSelected() && $value == 'No') || (!$this->isSelected() && $value == 'Yes')) {
            $this->click();
        }
    }
}

<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Client\Element;

/**
 * Class provides ability to work with page element radio button.
 *
 * @api
 */
class RadiobuttonElement extends SimpleElement
{
    /**
     * Get value of the required element.
     *
     * @return string
     */
    public function getValue()
    {
        $this->eventManager->dispatchEvent(['get_value'], [$this->getAbsoluteSelector()]);
        return $this->isSelected() ? 'Yes' : 'No';
    }

    /**
     * Click on radio button if value = 'Yes'.
     *
     * @param string $value
     * @return void
     */
    public function setValue($value)
    {
        $this->eventManager->dispatchEvent(['set_value'], [__METHOD__, $this->getAbsoluteSelector()]);
        if (!$this->isSelected() && $value == 'Yes') {
            $this->click();
        }
    }
}

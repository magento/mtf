<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Client\Element;

/**
 * Class provides an ability to work with page element multiselect which select strict values.
 *
 * @api
 */
class StrictmultiselectElement extends MultiselectElement
{
    /**
     * Option locator by value.
     *
     * @var string
     */
    protected $optionByValue = './/option[text() = %s]';
}

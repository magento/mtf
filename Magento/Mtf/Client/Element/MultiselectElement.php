<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Mtf\Client\Element;

use Magento\Mtf\Client\Locator;

/**
 * Class provides ability to work with page element multiselect
 * (such as setting/getting value, clicking, drag-n-drop element, etc).
 *
 * @api
 */
class MultiselectElement extends SelectElement
{
    /**
     * Get value of the selected option of the element.
     *
     * @return array
     */
    public function getValue()
    {
        return $this->getText();
    }

    /**
     * Get Selected Options Labels.
     *
     * @return array
     */
    public function getText()
    {
        $this->eventManager->dispatchEvent(['get_value'], [__METHOD__, $this->getAbsoluteSelector()]);
        $result = [];
        $tagName = 'option';
        $options = $this->getElements($tagName, Locator::SELECTOR_XPATH);

        /** @var SimpleElement $option */
        foreach ($options as $option) {
            if ($option->isSelected()) {
                $result[] = $option->getText();
            }
        }

        return $result;
    }

    /**
     * Select Options by Label in Multiple Select.
     *
     * @param string|array $values
     * @return void
     * @throws \Exception
     */
    public function setValue($values)
    {
        $this->eventManager->dispatchEvent(['set_value'], [__METHOD__, $this->getAbsoluteSelector()]);
        $this->deselectAll();
        foreach ((array)$values as $value) {
            $matched = false;

            $options = $this->getElements(sprintf($this->optionByValue, $value), Locator::SELECTOR_XPATH);

            /** @var SimpleElement $option */
            foreach ($options as $option) {
                if (!$option->isSelected()) {
                    $option->click();
                }
                $matched = true;
            }

            if (!$matched) {
                throw new \Exception(sprintf('Cannot locate option with value: %s', $value));
            }
        }
    }

    /**
     * Deselect all options in multiple select tag.
     *
     * @throws \Exception
     * @return void
     */
    public function deselectAll() {
        $options = $this->getElements('option[selected]');
        /** @var SimpleElement $option */
        foreach ($options as $option) {
            if ($option->isSelected()) {
                $option->click();
            }
        }
    }
}

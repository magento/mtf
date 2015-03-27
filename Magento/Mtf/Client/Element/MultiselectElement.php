<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
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

            $options = $this->getElements(
                sprintf($this->optionByValue, $this->escapeQuotes($value)),
                Locator::SELECTOR_XPATH
            );

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

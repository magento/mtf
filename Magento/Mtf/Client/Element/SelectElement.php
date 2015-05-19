<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Client\Element;

use Magento\Mtf\Client\ElementInterface;
use Magento\Mtf\Client\Locator;

/**
 * Typified element class for Select elements.
 *
 * @api
 */
class SelectElement extends SimpleElement
{
    /**
     * Selector for selected option.
     *
     * @var string
     */
    protected $selectedOption = 'option:checked';

    /**
     * Option locator by value.
     *
     * @var string
     */
    protected $optionByValue = './/option[contains(normalize-space(.), %s)]';

    /**
     * Set the value.
     *
     * @param string|array $value
     * @throws \Exception
     * @return void
     */
    public function setValue($value)
    {
        $this->eventManager->dispatchEvent(['set_value'], [__METHOD__, $this->getAbsoluteSelector()]);
        $option = $this->find(sprintf($this->optionByValue, $this->escapeQuotes($value)), Locator::SELECTOR_XPATH);
        $option->click();
    }

    /**
     * Get value of the selected option of the element.
     *
     * @return string
     */
    public function getValue()
    {
        $this->eventManager->dispatchEvent(['get_value'], [__METHOD__, $this->getAbsoluteSelector()]);

        $element = $this->find($this->selectedOption);
        $value = $element->isVisible() ? $element->getText() : '';

        return $value;
    }

    /**
     * Get label of the selected option of the element.
     *
     * @return string
     */
    public function getText()
    {
        return $this->getValue();
    }

    /**
     * Drag'n'drop method is not accessible in this class.
     * Throws exception if used.
     *
     * @param ElementInterface $target
     * @throws \Exception
     * @return void
     */
    public function dragAndDrop(ElementInterface $target)
    {
        throw new \Exception('Not applicable for this class of elements (SelectElement)');
    }

    /**
     * Convert strings with both quotes and ticks into:
     *   foo'"bar -> concat("foo'", '"', "bar")
     *
     * @param string $toEscape The string to be converted.
     * @return string The escaped string.
     */
    protected function escapeQuotes($toEscape)
    {
        if (strpos($toEscape, '"') !== false && strpos($toEscape, "'" != false)) {
            $subStrings = explode('"', $toEscape);
            $escaped = "concat(";
            $first = true;
            foreach ($subStrings as $string) {
                if (!$first) {
                    $escaped .= ", '\"',";
                    $first = false;
                }
                $escaped .= '"' . $string . '"';
            }

            return $escaped;
        }

        if (strpos($toEscape, '"' !== false)) {
            return sprintf("'%s'", $toEscape);
        }

        return sprintf('"%s"', $toEscape);
    }
}

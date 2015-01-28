<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Mtf\Client\Element;

use Magento\Mtf\Client\ElementInterface;
use Magento\Mtf\Client\Locator;

/**
 * Class SelectElement
 * Typified element class for Select elements
 *
 * @api
 */
class SelectElement extends SimpleElement
{
    /**
     * Option locator
     *
     * @var string
     */
    protected $optionByIndex = './option[%d]';

    /**
     * Set the value
     *
     * @param string|array $value
     * @throws \Exception
     * @return void
     */
    public function setValue($value)
    {
        $this->eventManager->dispatchEvent(['set_value'], [__METHOD__, $this->getAbsoluteSelector()]);
        $xpath = './/option[contains(normalize-space(.), ' . $this->escapeQuotes($value) . ')]';
        $option = $this->find($xpath, Locator::SELECTOR_XPATH);
        $option->click();
    }

    /**
     * Select value in dropdown which has option groups
     *
     * @param string $optionGroup
     * @param string $value
     * @throws \Exception
     * @return void
     */
    public function setOptionGroupValue($optionGroup, $value)
    {
        $this->eventManager->dispatchEvent(['set_value'], [__METHOD__, $this->getAbsoluteSelector()]);
        $xpath = ".//optgroup[contains(@label,'$optionGroup')]/option[contains(text(), "
            . $this->escapeQuotes($value) . ")]";
        $option = $this->find($xpath, Locator::SELECTOR_XPATH);
        $option->click();
    }

    /**
     * Get value of the selected option of the element
     *
     * @return string
     */
    public function getValue()
    {
        $this->eventManager->dispatchEvent(['get_value'], [__METHOD__, $this->getAbsoluteSelector()]);

        $index = 1;
        $option = $this->find(sprintf($this->optionByIndex, $index), Locator::SELECTOR_XPATH);
        while ($option->isVisible()) {
            if ($option->isSelected()) {
                return $option->getText();
            }

            ++$index;
            $option = $this->find(sprintf($this->optionByIndex, $index), Locator::SELECTOR_XPATH);
        }

        return '';
    }

    /**
     * Get label of the selected option of the element
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

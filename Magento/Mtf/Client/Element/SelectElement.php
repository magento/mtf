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
     * Option locator by index.
     *
     * @var string
     */
    protected $optionByIndex = './option[%d]';

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

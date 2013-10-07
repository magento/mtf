<?php
/**
 * {license_notice}
 *
 * @api
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Block;

use Mtf\Block as BlockInterface;
use Mtf\Client\Driver\Selenium\Element;
use Mtf;

/**
 * Class Block
 *
 * Is used for any blocks on the page
 * Classes which implement this interface are expected to provide public methods
 * to perform all possible interactions with the corresponding part of the page.
 * Blocks provide additional level of granularity of tests for business logic encapsulation
 * (extending Page Object concept).
 *
 * @package Mtf\Block
 * @abstract
 */
abstract class Block implements BlockInterface
{
    /**
     * The root element of the block
     *
     * @var Mtf\Client\Element
     */
    protected $_rootElement;

    /**
     * @constructor
     * @param Element $element
     */
    public function __construct(Element $element)
    {
        $this->_rootElement = $element;
        $this->_init();
    }

    /**
     * Initialize for children classes
     */
    protected function _init()
    {
        //
    }

    /**
     * Check if the root element of the block is visible or not
     *
     * @return boolean
     */
    public function isVisible()
    {
        return $this->_rootElement->isVisible();
    }

    /**
     * Wait for element is visible in the block
     */
    public function waitForElementVisible($selector, $strategy = Mtf\Client\Element\Locator::SELECTOR_CSS)
    {
        $browser = $this->_rootElement;
        return $browser->waitUntil(
            function () use ($browser, $selector, $strategy) {
                $productSavedMessage = $browser->find($selector, $strategy);
                return $productSavedMessage->isVisible() ? true : null;
            }
        );
    }

    /**
     * Wait for element is visible in the block
     */
    public function waitForElementNotVisible($selector, $strategy = Mtf\Client\Element\Locator::SELECTOR_CSS)
    {
        $browser = $this->_rootElement;
        return $browser->waitUntil(
            function () use ($browser, $selector, $strategy) {
                $productSavedMessage = $browser->find($selector, $strategy);
                return $productSavedMessage->isVisible() == false ? true : null;
            }
        );
    }
}

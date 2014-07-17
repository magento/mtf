<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Block;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

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
 * @api
 */
abstract class Block implements BlockInterface
{
    /**
     * The root element of the block
     *
     * @var Element
     */
    protected $_rootElement;

    /**
     * Factory for creating Blocks
     *
     * @var BlockFactory
     */
    protected $blockFactory;

    /**
     * @constructor
     * @param Element $element
     * @param BlockFactory $blockFactory
     */
    public function __construct(Element $element, BlockFactory $blockFactory)
    {
        $this->_rootElement = $element;
        $this->blockFactory = $blockFactory;

        $this->_init();
    }

    /**
     * Element reinitialization in order to keep operability of block after page reload
     *
     * @return Block
     */
    public function reinitRootElement()
    {
        $this->_rootElement = clone $this->_rootElement;
        return $this;
    }

    /**
     * Initialize for children classes
     * @return void
     */
    protected function _init()
    {
        //
    }

    /**
     * Check if the root element of the block is visible or not
     *
     * @return bool
     */
    public function isVisible()
    {
        return $this->_rootElement->isVisible();
    }

    /**
     * Wait for element is visible in the block
     *
     * @param string $selector
     * @param string $strategy
     * @return bool|null
     */
    public function waitForElementVisible($selector, $strategy = Locator::SELECTOR_CSS)
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
     *
     * @param string $selector
     * @param string $strategy
     * @return bool|null
     */
    public function waitForElementNotVisible($selector, $strategy = Locator::SELECTOR_CSS)
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

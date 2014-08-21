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
use Mtf\Client\Driver\Selenium\Browser;

/**
 * Class Block
 *
 * Is used for any blocks on the page
 * Classes which implement this interface are expected to provide public methods
 * to perform all possible interactions with the corresponding part of the page.
 * Blocks provide additional level of granularity of tests for business logic encapsulation
 * (extending Page Object concept).
 *
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
     * Provides ability to perform browser actions
     *
     * @var Browser
     */
    protected $browser;

    /**
     * Block config
     *
     * @var array
     */
    protected $config;

    /**
     * Block render instances
     *
     * @var array
     */
    protected $renderInstances = [];

    /**
     * @constructor
     * @param Element $element
     * @param BlockFactory $blockFactory
     * @param Browser $browser
     * @param array $config
     */
    public function __construct(Element $element, BlockFactory $blockFactory, Browser $browser, array $config = [])
    {
        $this->_rootElement = $element;
        $this->blockFactory = $blockFactory;
        $this->browser = $browser;
        $this->config = $config;

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

    /**
     * Call render block
     *
     * @param string $type
     * @param string $method
     * @param array $arguments
     * @return bool
     */
    protected function callRender($type, $method, array $arguments = [])
    {
        $block = $this->getRenderInstance($type);
        if ($block !== null) {
            call_user_func_array([$block, $method], $arguments);
            return true;
        }
        return false;
    }

    /**
     * Get render instance by name
     *
     * @param string $renderName
     * @return BlockInterface
     */
    protected function getRenderInstance($renderName)
    {
        if (!isset($this->renderInstances[$renderName])) {
            $blockMeta = isset($this->config['renders'][$renderName]) ? $this->config['renders'][$renderName] : [];
            $class = isset($blockMeta['class']) ? $blockMeta['class'] : false;
            if ($class) {
                $element = $this->_rootElement->find($blockMeta['locator'], $blockMeta['strategy']);
                $config = [
                    'renders' => isset($blockMeta['renders']) ? $blockMeta['renders'] : []
                ];
                $block = $this->blockFactory->create(
                    $class,
                    [
                        'element' => $element,
                        'config' => $config
                    ]
                );
            } else {
                return null;
            }

            $this->renderInstances[$renderName] = $block;
        }
        return $this->renderInstances[$renderName];
    }
}

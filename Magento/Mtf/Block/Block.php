<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Block;

use Magento\Mtf\Client\Locator;
use Magento\Mtf\Client\BrowserInterface;
use Magento\Mtf\Client\ElementInterface;

/**
 * Is used for any blocks on the page.
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
     * @var ElementInterface
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
     * @var BrowserInterface
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
     * @param ElementInterface $element
     * @param BlockFactory $blockFactory
     * @param BrowserInterface $browser
     * @param array $config
     */
    public function __construct(
        ElementInterface $element,
        BlockFactory $blockFactory,
        BrowserInterface $browser,
        array $config = []
    ) {
        $this->_rootElement = $element;
        $this->blockFactory = $blockFactory;
        $this->browser = $browser;
        $this->config = $config;

        $this->init();
    }

    /**
     * Initialize for children classes
     * @return void
     */
    protected function init()
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
        $browser = $this->browser;
        return $browser->waitUntil(
            function () use ($browser, $selector, $strategy) {
                $element = $browser->find($selector, $strategy);
                return $element->isVisible() ? true : null;
            }
        );
    }

    /**
     * Wait for element is not visible in the block
     *
     * @param string $selector
     * @param string $strategy
     * @return bool|null
     */
    public function waitForElementNotVisible($selector, $strategy = Locator::SELECTOR_CSS)
    {
        $browser = $this->browser;
        return $browser->waitUntil(
            function () use ($browser, $selector, $strategy) {
                $element = $browser->find($selector, $strategy);
                return $element->isVisible() == false ? true : null;
            }
        );
    }

    /**
     * Check exist render type
     *
     * @param string $renderName
     * @return bool
     */
    protected function hasRender($renderName)
    {
        return isset($this->config['renders'][$renderName]);
    }

    /**
     * Call render block
     *
     * @param string $type
     * @param string $method
     * @param array $arguments
     * @return mixed
     * @throws \Exception
     */
    protected function callRender($type, $method, array $arguments = [])
    {
        $block = $this->getRenderInstance($type);
        if (null === $block) {
            throw new \Exception("Wrong render instance: \"{$type}\"");
        }

        return call_user_func_array([$block, $method], $arguments);
    }

    /**
     * Get render instance by name
     *
     * @param string $renderName
     * @return BlockInterface|null
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function getRenderInstance($renderName)
    {
        if (!isset($this->renderInstances[$renderName])) {
            $blockMeta = isset($this->config['renders'][$renderName]) ? $this->config['renders'][$renderName] : [];
            $class = isset($blockMeta['class']) ? $blockMeta['class'] : false;
            if ($class) {
                $element = (isset($blockMeta['locator']) && isset($blockMeta['strategy']))
                    ? $this->_rootElement->find($blockMeta['locator'], $blockMeta['strategy'])
                    : $this->_rootElement;
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

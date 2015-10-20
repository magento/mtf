<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Page;

use Magento\Mtf\Block\BlockFactory;
use Magento\Mtf\Block\BlockInterface;
use Magento\Mtf\Client\BrowserInterface;
use Magento\Mtf\Config\DataInterface;
use Magento\Mtf\System\Event\EventManagerInterface;

/**
 * Classes which implement this interface are expected to store all blocks of application page
 * and provide public getter methods to provide access to blocks.
 *
 * @api
 */
class Page implements PageInterface
{
    /**
     * Page url.
     */
    const MCA = '';

    /**
     * Client Browser.
     *
     * @var BrowserInterface
     */
    protected $browser;

    /**
     * Current page url.
     *
     * @var string
     */
    protected $url;

    /**
     * Configuration instance.
     *
     * @var DataInterface
     */
    protected $configData;

    /**
     * Page blocks definitions array.
     *
     * @var array
     */
    protected $blocks = [];

    /**
     * Page blocks instances.
     *
     * @var BlockInterface[]
     */
    protected $blockInstances = [];

    /**
     * Block Factory instance.
     *
     * @var BlockFactory
     */
    protected $blockFactory;

    /**
     * Event Manager instance.
     *
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * Set configuration instance, client browser and call initUrl method.
     *
     * @constructor
     * @param DataInterface $configData
     * @param BrowserInterface $browser
     * @param BlockFactory $blockFactory
     * @param EventManagerInterface $eventManager
     */
    public function __construct(
        DataInterface $configData,
        BrowserInterface $browser,
        BlockFactory $blockFactory,
        EventManagerInterface $eventManager
    ) {
        $this->configuData = $configData;
        $this->browser = $browser;
        $this->blockFactory = $blockFactory;
        $this->eventManager = $eventManager;

        $this->initUrl();
    }

    /**
     * Init page. Set page url.
     *
     * @return void
     */
    protected function initUrl()
    {
        //
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // General API

    /**
     * Open page using browser.
     *
     * @param array $params
     * @return $this
     */
    public function open(array $params = [])
    {
        $this->eventManager->dispatchEvent(['execution'], ['[page MCA: ' . static::MCA . ']']);
        $url = $this->url;

        foreach ($params as $paramName => $paramValue) {
            if (strpos($url, '?') !== false) {
                $url .= '&';
            } else {
                $url .= '?';
            }
            $url .= $paramName . '=' . $paramValue;
        }

        $this->browser->open($url);

        return $this;
    }

    /**
     * Retrieve an instance of block.
     *
     * @param string $blockName
     * @return BlockInterface
     * @throws \InvalidArgumentException
     */
    public function getBlockInstance($blockName)
    {
        $this->eventManager->dispatchEvent(['execution'], ['[page MCA: ' . static::MCA . ']']);
        if (!isset($this->blockInstances[$blockName])) {
            $blockMeta = isset($this->blocks[$blockName]) ? $this->blocks[$blockName] : [];
            $class = isset($blockMeta['class']) ? $blockMeta['class'] : false;
            if ($class) {
                $element = $this->browser->find($blockMeta['locator'], $blockMeta['strategy']);
                $config = [
                    'renders' => isset($blockMeta['render']) ? $blockMeta['render'] : []
                ];
                $block = $this->blockFactory->create(
                    $class,
                    [
                        'element' => $element,
                        'config' => $config
                    ]
                );
            } else {
                throw new \InvalidArgumentException(
                    sprintf('There is no such block "%s" declared for the page "%s" ', $blockName, $class)
                );
            }

            $this->blockInstances[$blockName] = $block;
        }

        return $this->blockInstances[$blockName];
    }
}

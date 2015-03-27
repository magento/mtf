<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Page;

use Magento\Mtf\Block\BlockFactory;
use Magento\Mtf\Block\BlockInterface;
use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Client\BrowserInterface;
use Magento\Mtf\Config\DataInterface;

/**
 * Class Page
 * Classes which implement this interface are expected to store all blocks of application page
 * and provide public getter methods to provide access to blocks
 *
 * @api
 */
class Page implements PageInterface
{
    /**
     * Page url
     */
    const MCA = '';

    /**
     * Client Browser
     *
     * @var BrowserInterface
     */
    protected $_browser;

    /**
     * Current page url
     *
     * @var string
     */
    protected $_url;

    /**
     * Configuration instance
     *
     * @var DataInterface
     */
    protected $_configData;

    /**
     * Page blocks definitions array
     *
     * @var array
     */
    protected $blocks = [];

    /**
     * Page blocks instances
     *
     * @var BlockInterface[]
     */
    protected $blockInstances = [];

    /**
     * Block Factory instance
     *
     * @var BlockFactory
     */
    protected $_blockFactory;

    /**
     * Constructor
     * Set configuration instance, client browser and call _init method
     *
     * @constructor
     * @param DataInterface $configData
     * @param BrowserInterface $browser
     * @param BlockFactory $blockFactory
     */
    public function __construct(
        DataInterface $configData,
        BrowserInterface $browser,
        BlockFactory $blockFactory
    )
    {
        $this->_configuData = $configData;
        $this->_browser = $browser;
        $this->_blockFactory = $blockFactory;

        $this->_init();
        $this->_initBlocks();
    }

    /**
     * @param string $property
     * @param mixed $value
     * @return void
     */
    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    /**
     * Init page. Set page url
     * @return void
     */
    protected function _init()
    {
        //
    }

    /**
     * @return void
     */
    protected function _initBlocks()
    {
        //
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // General API

    /**
     * Page initialization
     *
     * @param FixtureInterface $fixture
     * @return void
     */
    public function init(FixtureInterface $fixture)
    {
        //
    }

    /**
     * Open page using browser
     *
     * @param array $params
     * @return $this
     */
    public function open(array $params = [])
    {
        $url = $this->_url;

        foreach ($params as $paramName => $paramValue) {
            if (strpos($url, '?') !== false) {
                $url .= '&';
            } else {
                $url .= '?';
            }
            $url .= $paramName . '=' . $paramValue;
        }

        $this->_browser->open($url);

        return $this;
    }

    /**
     * Retrieve an instance of block
     *
     * @param string $blockName
     * @return BlockInterface
     * @throws \InvalidArgumentException
     */
    public function getBlockInstance($blockName)
    {
        if (!isset($this->blockInstances[$blockName])) {
            $blockMeta = isset($this->blocks[$blockName]) ? $this->blocks[$blockName] : [];
            $class = isset($blockMeta['class']) ? $blockMeta['class'] : false;
            if ($class) {
                $element = $this->_browser->find($blockMeta['locator'], $blockMeta['strategy']);
                $config = [
                    'renders' => isset($blockMeta['render']) ? $blockMeta['render'] : []
                ];
                $block = $this->_blockFactory->create(
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
        // @todo fix to get link to new page if page reloaded
        return $this->blockInstances[$blockName];
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Page;

use Mtf\Block\BlockFactory;
use Mtf\Block\BlockInterface;
use Mtf\Fixture\FixtureInterface;
use Mtf\Client\Browser;
use Mtf\System\Config;

/**
 * Class Page
 *
 * Classes which implement this interface are expected to store all blocks of application page
 * and provide public getter methods to provide access to blocks
 *
 * @package Mtf\Page
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
     * @var Browser
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
     * @var Config
     */
    protected $_configuration;

    /**
     * Page blocks definitions array
     *
     * @var array
     */
    protected $_blocks = [];

    /**
     * Page blocks instances
     *
     * @var BlockInterface[]
     */
    protected $_blockInstances = [];

    /**
     * @var BlockFactory
     */
    protected $_blockFactory;

    /**
     * Constructor
     * Set configuration instance, client browser and call _init method
     *
     * @constructor
     * @param Config $configuration
     * @param Browser $browser
     * @param BlockFactory $blockFactory
     */
    public function __construct(Config $configuration, Browser $browser, BlockFactory $blockFactory)
    {
        $this->_configuration = $configuration;
        $this->_browser = $browser;
        $this->_blockFactory = $blockFactory;

        $this->_init();
        $this->_initBlocks();
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    /**
     * Init page. Set page url
     */
    protected function _init()
    {
        //
    }

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
     * @param $blockName
     * @return BlockInterface
     * @throws \InvalidArgumentException
     */
    public function getBlockInstance($blockName)
    {
        if (!isset($this->_blockInstances[$blockName])) {
            $blockMeta = isset($this->_blocks[$blockName]) ? $this->_blocks[$blockName] : [];
            $class = isset($blockMeta['class']) ? $blockMeta['class'] : false;
            if ($class) {
                $element = $this->_browser->find($blockMeta['locator'], $blockMeta['strategy']);
                $block = $this->_blockFactory->create(
                    $class,
                    [
                        'element' => $element
                    ]
                );
            } else {
                throw new \InvalidArgumentException("There is no such block '{$blockName}' declared for the page "
                    . "'{$class}' ");
            }

            $this->_blockInstances[$blockName] = $block;
        }
        // @todo fix to get link to new page if page reloaded
        return $this->_blockInstances[$blockName]->reinitRootElement();
    }
}

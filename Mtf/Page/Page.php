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

namespace Mtf\Page;

use Mtf\Block\BlockFactory;
use Mtf\Block\BlockInterface;
use Mtf\Fixture\FixtureInterface;
use Mtf\Client\Browser;
use Mtf\System\Config;

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
                    'renders' => isset($blockMeta['renders']) ? $blockMeta['renders'] : []
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
        return $this->blockInstances[$blockName]->reinitRootElement();
    }
}

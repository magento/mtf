<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Page;

use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;
use Mtf\Page as PageInterface;
use Mtf\Client\Driver\Selenium\Browser;
use Mtf\System\Config;

/**
 * Class Page
 *
 * Classes which implement this interface are expected to store all blocks of application page
 * and provide public getter methods to provide access to blocks
 *
 * @package Mtf\Page
 */
class Page implements PageInterface
{
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
     * Constructor
     *
     * Set configuration instance, client browser and call _init method
     *
     * @param Config $configuration
     */
    final public function __construct(Config $configuration)
    {
        $this->_configuration = $configuration;

        $this->_browser = Factory::getClientBrowser();

        $this->_init();
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
        $this->_url = '';
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // General API

    /**
     * Page initialization
     *
     * @param DataFixture $fixture
     */
    public function init(DataFixture $fixture)
    {
        //
    }

    /**
     * Open page using browser
     *
     * @param array $params
     * @return $this
     */
    public function open(array $params = array())
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
}

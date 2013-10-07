<?php

namespace Mtf\Factory;

use Mtf\Factory as FactoryInterface;
use Mtf\System\Config;
use Mtf;

/**
 * Class Factory
 *
 * Factory class is responsible for providing static access to entities factories
 *
 * @package Mtf\Factory
 */
class Factory implements FactoryInterface
{
    /**
     * Configuration
     *
     * @var Config
     */
    protected static $_config;

    /**
     * Handlers Factory
     *
     * @var Mtf\Handler\HandlerFactory
     */
    protected static $_app;

    /**
     * @var Mtf\Client\Driver\Selenium\Element
     */
    protected static $_rootElement;

    /**
     * Client Browser instance
     *
     * @var Mtf\Client\Driver\Selenium\Browser
     */
    protected static $_clientBrowser;

    /**
     * Page Factory
     *
     * @var Mtf\Page\PageFactory
     */
    protected static $_pageFactory;

    /**
     * Block Factory
     *
     * @var Mtf\Block\BlockFactory
     */
    protected static $_blockFactory;

    /**
     * Fixture Factory
     *
     * @var Mtf\Fixture\FixtureFactory
     */
    protected static $_fixtureFactory;

    /**
     * Init configuration
     */
    public static function initConfig()
    {
        self::$_config = new Config();
    }

    /**
     * Init handler factory
     */
    public static function initApp()
    {
        $config = self::getConfig();
        self::$_app = new Mtf\Handler\HandlerFactory($config);
    }

    /**
     * Init client browser
     */
    public static function initClientBrowser()
    {
        $config = self::getConfig();
        $serverConfiguration = $config->getConfigParam('server');
        $arrayKeys = array_keys($serverConfiguration);
        $serverName = reset($arrayKeys);
        $browserClass = 'Mtf\\Client\\Driver\\' . ucfirst($serverName) . '\Browser';
        self::$_clientBrowser = new $browserClass($serverConfiguration[$serverName]);
    }

    /**
     * Init page factory
     */
    public static function initPageFactory()
    {
        $config = self::getConfig();
        self::$_pageFactory = new Mtf\Page\PageFactory($config);
    }

    /**
     * Init block factory
     */
    public static function initBlockFactory()
    {
        $config = self::getConfig();
        self::$_blockFactory = new Mtf\Block\BlockFactory($config);
    }

    /**
     * Init fixture factory
     */
    public static function initFixtureFactory()
    {
        $config = self::getConfig();
        self::$_fixtureFactory = new Mtf\Fixture\FixtureFactory($config);
    }

    /**
     * Get handlers factory
     *
     * @return Config
     */
    public static function getConfig()
    {
        if (!self::$_config) {
            self::initConfig();
        }
        return self::$_config;
    }

    /**
     * Get handlers factory
     *
     * @return Mtf\Handler\HandlerFactory
     */
    public static function getApp()
    {
        if (!self::$_app) {
            self::initApp();
        }
        return self::$_app;
    }

    /**
     * Get Client Browser
     *
     * @spi
     * @return Mtf\Client\Browser
     */
    public static function getClientBrowser()
    {
        if (!self::$_clientBrowser) {
            self::initClientBrowser();
        }
        return self::$_clientBrowser;
    }

    public static function toggleClientBrowser($browser)
    {
        self::$_clientBrowser = $browser;
    }

    /**
     * Get Page factory
     *
     * @api
     * @return Mtf\Page\PageFactory
     */
    public static function getPageFactory()
    {
        if (!self::$_pageFactory) {
            self::initPageFactory();
        }
        return self::$_pageFactory;
    }

    /**
     * Get block factory
     *
     * @api
     * @return Mtf\Block\BlockFactory
     */
    public static function getBlockFactory()
    {
        if (!self::$_blockFactory) {
            self::initBlockFactory();
        }
        return self::$_blockFactory;
    }

    /**
     * Get fixture factory
     *
     * @api
     * @return Mtf\Fixture\FixtureFactory
     */
    public static function getFixtureFactory()
    {
        if (!self::$_fixtureFactory) {
            self::initFixtureFactory();
        }
        return self::$_fixtureFactory;
    }
}

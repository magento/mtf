<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

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
 * @api
 * @deprecated
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
     * @var Mtf\Handler\HandlerFactoryDeprecated
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
     * @var Mtf\Page\PageFactoryDeprecated
     */
    protected static $_pageFactory;

    /**
     * Block Factory
     *
     * @var Mtf\Block\BlockFactoryDeprecated
     */
    protected static $_blockFactory;

    /**
     * Fixture Factory
     *
     * @var Mtf\Fixture\FixtureFactoryDeprecated
     */
    protected static $_fixtureFactory;

    /**
     * Repository Factory
     *
     * @var Mtf\Repository\RepositoryFactoryDeprecated
     */
    protected static $_repositoryFactory;

    /**
     * @var \Mtf\ObjectManager
     */
    protected static $_objectManager;

    /**
     * Init Object Manager
     */
    public static function initObjectManager()
    {
        self::$_objectManager = \Mtf\ObjectManager::getInstance();
    }

    /**
     * Init configuration
     */
    public static function initConfig()
    {
        self::$_config = self::getObjectManager()->get('Mtf\System\Config');
    }

    /**
     * Init handler factory
     */
    public static function initApp()
    {
        self::$_app = self::getObjectManager()->get('Mtf\Handler\HandlerFactoryDeprecated');
    }

    /**
     * Init client browser
     */
    public static function initClientBrowser()
    {
        self::$_clientBrowser = self::getObjectManager()->get('Mtf\Client\Driver\Selenium\Browser');
    }

    /**
     * Init page factory
     */
    public static function initPageFactory()
    {
        self::$_pageFactory = self::getObjectManager()->get('Mtf\Page\PageFactoryDeprecated');
    }

    /**
     * Init block factory
     */
    public static function initBlockFactory()
    {
        self::$_blockFactory = self::getObjectManager()->get('Mtf\Block\BlockFactoryDeprecated');
    }

    /**
     * Init fixture factory
     */
    public static function initFixtureFactory()
    {
        self::$_fixtureFactory = self::getObjectManager()->get('Mtf\Fixture\FixtureFactoryDeprecated');
    }

    /**
     * Init repository factory
     */
    public static function initRepositoryFactory()
    {
        self::$_repositoryFactory = self::getObjectManager()->get('Mtf\Repository\RepositoryFactoryDeprecated');
    }

    /**
     * Get Object Manager
     *
     * @return \Mtf\ObjectManager
     */
    public static function getObjectManager()
    {
        if (!self::$_objectManager) {
            self::initObjectManager();
        }
        return self::$_objectManager;
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
     * @return Mtf\Handler\HandlerFactoryDeprecated
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

    /**
     * Get Page factory
     *
     * @api
     * @return Mtf\Page\PageFactoryDeprecated
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
     * @return Mtf\Block\BlockFactoryDeprecated
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
     * @return Mtf\Fixture\FixtureFactoryDeprecated
     */
    public static function getFixtureFactory()
    {
        if (!self::$_fixtureFactory) {
            self::initFixtureFactory();
        }
        return self::$_fixtureFactory;
    }

    /**
     * Get repository factory
     *
     * @api
     * @return Mtf\Repository\RepositoryFactoryDeprecated
     */
    public static function getRepositoryFactory()
    {
        if (!self::$_repositoryFactory) {
            self::initRepositoryFactory();
        }
        return self::$_repositoryFactory;
    }
}

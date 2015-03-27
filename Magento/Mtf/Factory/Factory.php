<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Factory;

use Magento\Mtf;
use Magento\Mtf\Factory as FactoryInterface;

/**
 * Class Factory
 *
 * Factory class is responsible for providing static access to entities factories
 *
 * @api
 * @deprecated
 */
class Factory implements FactoryInterface
{
    /**
     * Handlers Factory
     *
     * @var \Magento\Mtf\Handler\HandlerFactoryDeprecated
     */
    protected static $_app;

    /**
     * @var \Magento\Mtf\Client\ElementInterface
     */
    protected static $_rootElement;

    /**
     * Client Browser instance
     *
     * @var \Magento\Mtf\Client\BrowserInterface
     */
    protected static $_clientBrowser;

    /**
     * Page Factory
     *
     * @var \Magento\Mtf\Page\PageFactoryDeprecated
     */
    protected static $_pageFactory;

    /**
     * Block Factory
     *
     * @var \Magento\Mtf\Block\BlockFactoryDeprecated
     */
    protected static $_blockFactory;

    /**
     * Fixture Factory
     *
     * @var \Magento\Mtf\Fixture\FixtureFactoryDeprecated
     */
    protected static $_fixtureFactory;

    /**
     * Repository Factory
     *
     * @var \Magento\Mtf\Repository\RepositoryFactoryDeprecated
     */
    protected static $_repositoryFactory;

    /**
     * Object Manager instance
     *
     * @var \Magento\Mtf\ObjectManager
     */
    protected static $_objectManager;

    /**
     * Init Object Manager
     *
     * @return void
     */
    public static function initObjectManager()
    {
        self::$_objectManager = \Magento\Mtf\ObjectManager::getInstance();
    }

    /**
     * Init handler factory
     *
     * @return void
     */
    public static function initApp()
    {
        self::$_app = self::getObjectManager()->get('Magento\Mtf\Handler\HandlerFactoryDeprecated');
    }

    /**
     * Init client browser
     *
     * @return void
     */
    public static function initClientBrowser()
    {
        self::$_clientBrowser = self::getObjectManager()->get('Magento\Mtf\Client\BrowserInterface');
    }

    /**
     * Init page factory
     *
     * @return void
     */
    public static function initPageFactory()
    {
        self::$_pageFactory = self::getObjectManager()->get('Magento\Mtf\Page\PageFactoryDeprecated');
    }

    /**
     * Init block factory
     *
     * @return void
     */
    public static function initBlockFactory()
    {
        self::$_blockFactory = self::getObjectManager()->get('Magento\Mtf\Block\BlockFactoryDeprecated');
    }

    /**
     * Init fixture factory
     *
     * @return void
     */
    public static function initFixtureFactory()
    {
        self::$_fixtureFactory = self::getObjectManager()->get('Magento\Mtf\Fixture\FixtureFactoryDeprecated');
    }

    /**
     * Init repository factory
     *
     * @return void
     */
    public static function initRepositoryFactory()
    {
        self::$_repositoryFactory = self::getObjectManager()->get('Magento\Mtf\Repository\RepositoryFactoryDeprecated');
    }

    /**
     * Get Object Manager
     *
     * @return \Magento\Mtf\ObjectManager
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
     * @return \Magento\Mtf\Handler\HandlerFactoryDeprecated
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
     * @return \Magento\Mtf\Client\BrowserInterface
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
     * @return \Magento\Mtf\Page\PageFactoryDeprecated
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
     * @return \Magento\Mtf\Block\BlockFactoryDeprecated
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
     * @return \Magento\Mtf\Fixture\FixtureFactoryDeprecated
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
     * @return \Magento\Mtf\Repository\RepositoryFactoryDeprecated
     */
    public static function getRepositoryFactory()
    {
        if (!self::$_repositoryFactory) {
            self::initRepositoryFactory();
        }
        return self::$_repositoryFactory;
    }
}

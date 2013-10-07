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

namespace Mtf;

/**
 * Interface for Factories
 *
 * @package Mtf
 */
interface Factory
{
    /**
     * Init handler factory
     */
    public static function initApp();

    /**
     * Get handler factory
     *
     * @return \Mtf\Handler\HandlerFactory
     */
    public static function getApp();

    /**
     * Init client browser
     */
    public static function initClientBrowser();

    /**
     * Get client browser
     *
     * @return \Mtf\Client\Browser
     */
    public static function getClientBrowser();

    /**
     * Init page factory
     */
    public static function initPageFactory();

    /**
     * Get page factory
     *
     * @return \Mtf\Page\PageFactory
     */
    public static function getPageFactory();

    /**
     * Init block factory
     */
    public static function initBlockFactory();

    /**
     * Get block factory
     *
     * @return \Mtf\Block\BlockFactory
     */
    public static function getBlockFactory();

    /**
     * Init fixture factory
     */
    public static function initFixtureFactory();

    /**
     * Get fixture factory
     *
     * @return \Mtf\Fixture\FixtureFactory
     */
    public static function getFixtureFactory();
}
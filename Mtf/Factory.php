<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf;

/**
 * Interface for Factories
 *
 * @deprecated
 */
interface Factory
{
    /**
     * Get handler factory
     *
     * @return \Mtf\Handler\HandlerFactoryDeprecated
     */
    public static function getApp();

    /**
     * Get page factory
     *
     * @return \Mtf\Page\PageFactoryDeprecated
     */
    public static function getPageFactory();

    /**
     * Get block factory
     *
     * @return \Mtf\Block\BlockFactoryDeprecated
     */
    public static function getBlockFactory();

    /**
     * Get fixture factory
     *
     * @return \Mtf\Fixture\FixtureFactoryDeprecated
     */
    public static function getFixtureFactory();

    /**
     * Get fixture factory
     *
     * @return \Mtf\Fixture\RepositoryFactoryDeprecated
     */
    public static function getRepositoryFactory();
}

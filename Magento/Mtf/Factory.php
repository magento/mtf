<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf;

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
     * @return \Magento\Mtf\Handler\HandlerFactoryDeprecated
     */
    public static function getApp();

    /**
     * Get page factory
     *
     * @return \Magento\Mtf\Page\PageFactoryDeprecated
     */
    public static function getPageFactory();

    /**
     * Get block factory
     *
     * @return \Magento\Mtf\Block\BlockFactoryDeprecated
     */
    public static function getBlockFactory();

    /**
     * Get fixture factory
     *
     * @return \Magento\Mtf\Fixture\FixtureFactoryDeprecated
     */
    public static function getFixtureFactory();

    /**
     * Get fixture factory
     *
     * @return \Magento\Mtf\Fixture\RepositoryFactoryDeprecated
     */
    public static function getRepositoryFactory();
}

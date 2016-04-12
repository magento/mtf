<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Client\Driver\Selenium\Driver;

use Magento\Mtf\Client\Driver\Selenium\RemoteDriver;

/**
 * Interface for load page.
 */
interface PageLoaderInterface
{
    /**
     * Set driver.
     *
     * @param RemoteDriver $driver
     * @return $this
     */
    public function setDriver(RemoteDriver $driver);

    /**
     * Wait page to load.
     *
     * @return void
     */
    public function wait();
}

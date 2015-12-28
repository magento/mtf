<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Client\Driver\Selenium\Driver;

/**
 * Interface for load page.
 */
interface PageLoaderInterface
{
    /**
     * Wait page to load.
     *
     * @return void
     */
    public function wait();
}

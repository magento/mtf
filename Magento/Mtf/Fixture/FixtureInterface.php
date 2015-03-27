<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Fixture;

use Magento\Mtf\Fixture;

/**
 * Interface for Fixture classes
 *
 * @api
 */
interface FixtureInterface
{
    /**
     * Persists prepared data into application
     * @return void
     */
    public function persist();

    /**
     * Return prepared data set
     *
     * @param string $key [optional]
     * @return mixed
     */
    public function getData($key = null);

    /**
     * Return data set configuration settings
     *
     * @return array
     */
    public function getDataConfig();
}

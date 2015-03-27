<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf;

/**
 * Interface for Fixture classes
 */
interface Fixture
{
    /**
     * Persists prepared data into application
     *
     * @return void
     * @throws \BadMethodCallException
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
     * @return string
     */
    public function getDataConfig();
}

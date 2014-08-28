<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Fixture;

use Mtf\Fixture;

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

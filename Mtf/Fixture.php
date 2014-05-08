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
 * Interface for Fixture classes
 *
 * @package Mtf
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
     * @param $key [optional]
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

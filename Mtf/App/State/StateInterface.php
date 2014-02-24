<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\App\State;

/**
 * Interface StateInterface
 *
 * Provides methods declaration for Application State classes.
 * Each of Application State Classes responsible for applying required changes to prepare Application State
 * according to it's configuration.
 *
 * @package Mtf\App\State
 * @api
 */
interface StateInterface
{
    /**
     * Apply set up configuration profile
     *
     * @return void
     */
    public function apply();

    /**
     * Clear instance under test
     *
     * @return void
     */
    public function clearInstance();

    /**
     * Get name of the Application State Profile
     *
     * @return string
     */
    public function getName();
}

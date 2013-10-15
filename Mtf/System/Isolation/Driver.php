<?php
/**
 * {license_notice}
 *
 * @api
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\System\Isolation;

/**
 * Interface Driver
 *
 * Interface of Isolation Driver, responsible for isolation mechanism realisation
 *
 * @package Mtf\System\Isolation
 */
interface Driver
{
    /**
     * Isolate mechanism realisation
     */
    public function isolate();
}

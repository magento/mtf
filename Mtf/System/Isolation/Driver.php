<?php
/**
 * {license_notice}
 *
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
 * @internal
 */
interface Driver
{
    /**
     * Isolate mechanism realisation
     */
    public function isolate();
}

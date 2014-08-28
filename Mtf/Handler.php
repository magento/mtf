<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf;

use Mtf\Fixture;

/**
 * Interface for Handlers
 */
interface Handler
{
    /**
     * Execute handler
     *
     * @param Fixture $fixture [optional]
     * @return mixed
     */
    public function execute(Fixture $fixture = null);
}

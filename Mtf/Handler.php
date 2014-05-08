<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf;

use Mtf\Fixture;

/**
 * Interface for Handlers
 *
 * @package Mtf
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

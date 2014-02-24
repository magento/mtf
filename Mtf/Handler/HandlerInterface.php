<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Handler;

use Mtf\Fixture\FixtureInterface;

/**
 * Interface for Handlers
 *
 * @package Mtf
 * @api
 */
interface HandlerInterface
{
    /**
     * Persist Fixture
     *
     * @param FixtureInterface $fixture [optional]
     * @return mixed
     */
    public function persist(FixtureInterface $fixture = null);
}

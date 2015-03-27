<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Handler;

use Magento\Mtf\Fixture\FixtureInterface;

/**
 * Interface for Handlers
 *
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

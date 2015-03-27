<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf;

use Magento\Mtf\Fixture;

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

<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\System\Isolation;

/**
 * Interface Driver
 * Interface of Isolation Driver, responsible for isolation mechanism realisation
 *
 * @internal
 */
interface Driver
{
    /**
     * Isolate mechanism realisation
     *
     * @return void
     */
    public function isolate();
}

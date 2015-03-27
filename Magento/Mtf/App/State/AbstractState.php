<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\App\State;

/**
 * Abstract class AbstractState
 *
 * @api
 */
abstract class AbstractState implements StateInterface
{
    /**
     * Specifies whether to clean instance under test
     *
     * @var bool
     */
    protected $isCleanInstance = false;

    /**
     * Apply set up configuration profile
     *
     * @return void
     */
    public function apply()
    {
        if ($this->isCleanInstance) {
            $this->clearInstance();
        }
    }

    /**
     * Clear instance under test
     *
     * @return void
     */
    public function clearInstance()
    {
        //
    }
}

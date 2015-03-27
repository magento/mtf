<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\App\State;

/**
 * Class State2
 * Example Application State class
 */
class State2 extends AbstractState
{
    /**
     * Apply set up configuration profile
     *
     * @return void
     */
    public function apply()
    {
        // apply configuration, install extensions, switch to design theme
    }

    /**
     * Get name of the Application State Profile
     *
     * @return string
     */
    public function getName()
    {
        return 'Configuration Profile #2';
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\App\State;

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

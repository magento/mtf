<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\App\State;

/**
 * Class State1
 * Example Application State class
 *
 * @package Mtf\App\State
 */
class State1 extends AbstractState
{
    /**
     * @inheritdoc
     */
    public function apply()
    {
        parent::apply();
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Configuration Profile #1';
    }
}

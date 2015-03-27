<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Test\Handler\Test;

use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Handler\Ui as AbstractUi;

/**
 * Class Ui
 */
class Ui extends AbstractUi implements TestInterface
{
    /**
     * @param FixtureInterface $fixture
     * @return void
     */
    public function persist(FixtureInterface $fixture = null)
    {
        //
    }
}

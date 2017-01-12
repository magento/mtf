<?php
/**
 * Copyright © 2017 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Test\Handler\Test;

use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Handler\Ui as AbstractUi;

/**
 * UI handler for Test fixture.
 */
class Ui extends AbstractUi implements TestInterface
{
    /**
     * Persist Test fixture.
     *
     * @param FixtureInterface $fixture
     * @return void
     */
    public function persist(FixtureInterface $fixture = null)
    {
        //
    }
}

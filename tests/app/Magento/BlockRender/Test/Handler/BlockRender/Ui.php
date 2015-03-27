<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\BlockRender\Test\Handler\BlockRender;

use Magento\BlockRender\Test\Handler\BlockRender\BlockRenderInterface;
use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Handler\Ui as AbstractUi;

/**
 * Class Ui
 */
class Ui extends AbstractUi implements BlockRenderInterface
{
   public function persist(FixtureInterface $fixture = null)
    {
        //
    }
}

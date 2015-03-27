<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace \Test\Handler\BlockRenderMtf;

use \Test\Handler\BlockRenderMtf\BlockRenderMtfInterface;
use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Handler\Ui as AbstractUi;

/**
 * Class Ui
 */
class Ui extends AbstractUi implements BlockRenderMtfInterface
{
   public function persist(FixtureInterface $fixture = null)
    {
        //
    }
}

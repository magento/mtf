<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Tag\Test\Handler\Tag;

use Magento\Tag\Test\Handler\Tag\TagInterface;
use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Handler\Ui as AbstractUi;

/**
 * Class Ui
 */
class Ui extends AbstractUi implements TagInterface
{
   public function persist(FixtureInterface $fixture = null)
    {
        //
    }
}

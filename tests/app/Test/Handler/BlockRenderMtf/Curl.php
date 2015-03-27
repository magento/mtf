<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace \Test\Handler\BlockRenderMtf;

use \Test\Handler\BlockRenderMtf\BlockRenderMtfInterface;
use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Handler\Curl as AbstractCurl;

/**
 * Class Curl
 */
class Curl extends AbstractCurl implements BlockRenderMtfInterface
{
   public function persist(FixtureInterface $fixture = null)
    {
        //
    }
}

<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace \Test\Handler\Fixture;

use \Test\Handler\Fixture\FixtureInterface;
use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Handler\Curl as AbstractCurl;

/**
 * Class Curl
 */
class Curl extends AbstractCurl implements FixtureInterface
{
   public function persist(FixtureInterface $fixture = null)
    {
        //
    }
}

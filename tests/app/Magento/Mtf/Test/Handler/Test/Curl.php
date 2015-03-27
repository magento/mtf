<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Test\Handler\Test;

use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Handler\Curl as AbstractCurl;

/**
 * Class Curl
 */
class Curl extends AbstractCurl implements TestInterface
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

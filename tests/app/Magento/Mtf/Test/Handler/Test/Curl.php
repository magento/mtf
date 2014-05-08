<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Mtf\Test\Handler\Test;

use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Curl as AbstractCurl;

/**
 * Class Curl
 *
 * @package Magento\Mtf\Test\Handler\Test
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

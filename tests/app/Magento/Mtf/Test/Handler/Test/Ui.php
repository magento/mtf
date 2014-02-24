<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Mtf\Test\Handler\Test; 

use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Ui as AbstractUi;

/**
 * Class Ui
 *
 * @package Magento\Mtf\Test\Handler\Test
 */
class Ui extends AbstractUi implements TestInterface
{
   public function persist(FixtureInterface $fixture = null)
    {
        //
    }
}

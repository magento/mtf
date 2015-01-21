<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Mtf\Test\TestCase;

use Magento\Mtf\TestCase\Functional;

/**
 * Class RegularTestCase
 */
class RegularTestCase extends Functional
{
    /**
     * @depends Magento\Mtf\Test\TestCase\InjectableTestCase::test1
     * @return void
     */
    public function test1()
    {
        /** @var $fixture \Magento\Mtf\Test\Fixture\Test */
        $fixture = $this->objectManager->create('Magento\Mtf\Test\Fixture\Test');

        /** @var $page \Magento\BlockRender\Test\Page\Area\TestPage */
        $page = $this->objectManager->create('Magento\BlockRender\Test\Page\Area\TestPage');

        $page->open();
        $page->getTestBlock()->click($fixture);
        sleep(1);
    }

    /**
     * @dataProvider dataProvider
     * @param string $fromDataProvider
     * @return void
     */
    public function test2($fromDataProvider)
    {
        var_dump($fromDataProvider . " works well!");
        /** @var $fixture \Magento\Mtf\Test\Fixture\Test */
        $fixture = $this->objectManager->create('Magento\Mtf\Test\Fixture\Test');

        /** @var $page \Magento\BlockRender\Test\Page\Area\TestPage */
        $page = $this->objectManager->create('Magento\BlockRender\Test\Page\Area\TestPage');

        $page->open();
        $page->getTestBlock()->click($fixture);
        sleep(1);
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        return array(
            'Variation #1' => array('Data Provider (1) for Regular Test Case'),
            'Variation #2' => array('Data Provider (2) for Regular Test Case')
        );
    }
}

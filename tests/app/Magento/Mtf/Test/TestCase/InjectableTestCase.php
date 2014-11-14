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

use Magento\Mtf\Test\Fixture\Test;
use Mtf\TestCase\Injectable;
use Magento\BlockRender\Test\Page\Area\TestPage;

/**
 * Class InjectableTestCase
 */
class InjectableTestCase extends Injectable
{
    /**
     * @param TestPage $page
     * @param Test $fixture
     * @return void
     */
    public function test1(TestPage $page, Test $fixture)
    {
        $page->open();
        $page->getTestBlock()->click($fixture);
        sleep(2);
    }

    /**
     * @param TestPage $page
     * @param Test $fixture
     * @return void
     */
    public function test2(TestPage $page, Test $fixture)
    {
        $page->open();
        $page->getTestBlock()->click($fixture);
        sleep(2);
    }

    /**
     * @param string $fromDataProvider
     * @dataProvider dataProvider
     * @return void
     */
    public function test3($fromDataProvider = '')
    {
        var_dump($fromDataProvider . " works well!");
        sleep(2);
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        return array(
            'Variation #1' => array('Data Variation 1 for Injectable Test Case'),
            'Variation #2' => array('Data Variation 2 for Injectable Test Case')
        );
    }

    /**
     * Incomplete Test
     * @return void
     */
    public function test4()
    {
        $this->markTestIncomplete('Incomplete Test');
    }

    /**
     * Incomplete Test
     * @return void
     */
    public function test5()
    {
        $this->markTestSkipped('Skipped Test');
    }

    /**
     * Filtered Test, see TestRunner.xml
     *
     * @param \Mtf\ObjectManager $objectManager
     * @return void
     */
    public function test6(\Mtf\ObjectManager $objectManager)
    {
        //
    }

    /**
     * Test form filling
     *
     * @param TestPage $page
     * @param Test $fixture
     * @return void
     */
    public function test7(TestPage $page, Test $fixture)
    {
        $page->open();
        $page->getTestBlock()->search($fixture);
        sleep(2);
    }
}

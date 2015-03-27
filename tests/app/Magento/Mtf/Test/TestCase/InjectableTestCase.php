<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Test\TestCase;

use Magento\Mtf\Test\Fixture\Test;
use Magento\Mtf\TestCase\Injectable;
use Magento\BlockRender\Test\Page\Area\TestPage;
use Magento\Mtf\ObjectManager;

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
        return [
            'Variation #1' => ['Data Variation 1 for Injectable Test Case'],
            'Variation #2' => ['Data Variation 2 for Injectable Test Case']
        ];
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
     * @param ObjectManager $objectManager
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function test6(ObjectManager $objectManager)
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

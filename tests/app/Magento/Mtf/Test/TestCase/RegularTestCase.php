<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
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
        return [
            'Variation #1' => ['Data Provider (1) for Regular Test Case'],
            'Variation #2' => ['Data Provider (2) for Regular Test Case']
        ];
    }
}

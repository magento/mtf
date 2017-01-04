<?php
/**
 * Copyright © 2017 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\BlockRender\Test\TestCase;

use Magento\Mtf\TestCase\Injectable;
use Magento\BlockRender\Test\Page\Area\TestPage;
use Magento\BlockRender\Test\Fixture\BlockRender;

/**
 * Test Block render functionality.
 */
class BlockRenderTest extends Injectable
{
    /**
     * Test proxy render
     *
     * @param TestPage $testPage
     * @param BlockRender $blockRender
     * @return void
     */
    public function test2(TestPage $testPage, BlockRender $blockRender)
    {
        $testPage->open();
        $testPage->getBlockRender()->render($blockRender);
        sleep(2);
    }
}

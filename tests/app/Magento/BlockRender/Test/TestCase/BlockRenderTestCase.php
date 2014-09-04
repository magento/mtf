<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Mtf\Test\TestCase;

use Magento\BlockRender\Test\Page\Area\TestPage;
use Mtf\TestCase\Injectable;
use Magento\Mtf\Test\Fixture\Test;
use Magento\BlockRender\Test\Fixture\BlockRender;

/**
 * Class RenderTestCase
 */
class RenderTestCase extends Injectable
{
    /**
     * Test proxy render #1
     *
     * @param TestPage $testPage
     * @param Test $test
     * @return void
     */
    public function test1(TestPage $testPage, Test $test)
    {
        $testPage->open();
        $testPage->getBlockRender()->render($test);
        sleep(3);
    }

    /**
     * Test proxy render #2
     *
     * @param TestPage $testPage
     * @param BlockRender $blockRender
     * @return void
     */
    public function test2(TestPage $testPage, BlockRender $blockRender)
    {
        $testPage->open();
        $testPage->getBlockRender()->render($blockRender);
        sleep(3);
    }
}

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

namespace Magento\BlockRender\Test\TestCase;

use Magento\Mtf\TestCase\Injectable;
use Magento\Mtf\Test\Fixture\Test;
use Magento\BlockRender\Test\Page\Area\TestPage;
use Magento\BlockRender\Test\Fixture\BlockRender;

/**
 * Class BlockRenderTestCase
 */
class BlockRenderTestCase extends Injectable
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

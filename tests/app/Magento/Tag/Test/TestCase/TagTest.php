<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Tag\Test\TestCase;

use Magento\Mtf\TestCase\Injectable;
use Magento\Mtf\Test\Fixture\Test;
use Magento\BlockRender\Test\Page\Area\TestPage;

/**
 * Test for check tagging functional.
 */
class TagTest extends Injectable
{
    /* tags */
    const SEVERITY = 'middle';
    /* end tags */

    /**
     * Run test.
     *
     * @param TestPage $page
     * @param Test $fixture
     * @return void
     */
    public function test(TestPage $page, Test $fixture)
    {
        $page->open();
        $page->getTestBlock()->search($fixture);
        sleep(2);
    }
}

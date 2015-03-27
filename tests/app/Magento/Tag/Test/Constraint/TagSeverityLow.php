<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Tag\Test\Constraint;

use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\Test\Fixture\Test;
use Magento\Mtf\Constraint\AbstractConstraint;
use Magento\BlockRender\Test\Page\Area\TestPage;

/**
 * Test filtering constraint by tags.
 */
class TagSeverityLow extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Test filtering constraint by tags.
     *
     * @param TestPage $page
     * @param Test $fixture
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function processAssert(TestPage $page, Test $fixture, FixtureFactory $fixtureFactory)
    {
        $data = $fixture->getData();

        $data['search'] .= sprintf(' constraint:severity:%s', self::SEVERITY);
        $reinitedFixture = $fixtureFactory->create('Magento\Mtf\Test\Fixture\Test', ['data' => $data]);

        $page->open();
        $page->getTestBlock()->fill($reinitedFixture);
        sleep(3);
    }

    /**
     * Text run constraint.
     *
     * @return string
     */
    public function toString()
    {
        return 'Run constraint with low severity.';
    }
}

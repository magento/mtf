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

namespace Magento\Tag\Test\Constraint;

use Mtf\Fixture\FixtureFactory;
use Magento\Mtf\Test\Fixture\Test;
use Mtf\Constraint\AbstractConstraint;
use Magento\BlockRender\Test\Page\Area\TestPage;

/**
 * Test filtering constraint by tags.
 */
class TagSeverityMiddle extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'middle';
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
        return 'Run constraint with middle severeness.';
    }
}

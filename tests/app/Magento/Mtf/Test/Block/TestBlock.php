<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Test\Block;

use Magento\Mtf\Block\Form;
use Magento\Mtf\Test\Fixture\Test;
use Magento\Mtf\Fixture\FixtureInterface;

/**
 * Class TestBlock
 */
class TestBlock extends Form
{
    /**
     * Array of placeholders applied on selector
     *
     * @var array
     */
    protected $placeholders = [
        'placeholder' => 'q'
    ];

    /**
     * Click on element
     *
     * @param Test $fixture
     * @return void
     */
    public function click(Test $fixture)
    {
        $locator = $fixture->getLocator();
        $strategy = $fixture->getStrategy();
        $this->_rootElement->find($locator, $strategy)->click();
    }

    /**
     * Perform search
     *
     * @param FixtureInterface $fixture
     * @return void
     */
    public function search(FixtureInterface $fixture)
    {
        $this->fill($fixture);
    }
}

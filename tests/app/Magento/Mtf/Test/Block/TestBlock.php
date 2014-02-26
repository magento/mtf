<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Mtf\Test\Block;

use Mtf\Block\Form;
use Magento\Mtf\Test\Fixture\Test;

/**
 * Class TestBlock
 *
 * @package Magento\Mtf\Test\Block
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
     * @param $fixture
     */
    public function search($fixture)
    {
        $this->fill($fixture);
    }
}
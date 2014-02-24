<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Mtf\Test\Block;

use Mtf\Block\Block;
use Magento\Mtf\Test\Fixture\Test;

/**
 * Class TestBlock
 *
 * @package Magento\Mtf\Test\Block
 */
class TestBlock extends Block
{
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
}

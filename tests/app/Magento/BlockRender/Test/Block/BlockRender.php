<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\BlockRender\Test\Block;

use Mtf\Block\Form;
use Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Test\Fixture\Test;

/**
 * Class BlockRender
 * Block for manage render form
 */
class BlockRender extends Form
{
    /**
     * Perform render
     *
     * @param FixtureInterface $fixture
     * @return void
     */
    public function render(FixtureInterface $fixture)
    {
        $this->callRender($fixture->getType(), 'fill', ['fixture' => $fixture]);
    }
}

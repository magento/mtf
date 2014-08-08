<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\BlockRender\Test\Page\Area;

use Mtf\Page\FrontendPage;

/**
 * Class TestPage
 */
class TestPage extends FrontendPage
{
    const MCA = '/';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'blockRender' => [
            'class' => 'Magento\BlockRender\Test\Block\BlockRender',
            'locator' => 'body',
            'strategy' => 'tag name',
            'renders' => [
                'test' => [
                    'class' => 'Magento\Mtf\Test\Block\TestBlock',
                    'locator' => '#viewport',
                    'strategy' => 'css selector',
                ],
                'render' => [
                    'class' => 'Magento\BlockRender\Test\Block\BlockRenderForm',
                    'locator' => '#viewport',
                    'strategy' => 'css selector',
                ],
            ],
        ],
        'testBlock' => [
            'class' => 'Magento\Mtf\Test\Block\TestBlock',
            'locator' => 'body',
            'strategy' => 'tag name',
        ],
    ];

    /**
     * @return \Magento\BlockRender\Test\Block\BlockRender
     */
    public function getBlockRender()
    {
        return $this->getBlockInstance('blockRender');
    }

    /**
     * @return \Magento\Mtf\Test\Block\TestBlock
     */
    public function getTestBlock()
    {
        return $this->getBlockInstance('testBlock');
    }
}

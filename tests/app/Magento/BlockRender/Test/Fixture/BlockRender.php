<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\BlockRender\Test\Fixture;

use Magento\Mtf\Test\Fixture\Test;

/**
 * Class BlockRender
 */
class BlockRender extends Test
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\BlockRender\Test\Repository\BlockRender';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\BlockRender\Test\Handler\BlockRender\BlockRenderInterface';

    /**
     * @var array
     */
    protected $defaultDataSet = [
        'locator' => null,
        'strategy' => null,
        'search' => 'Block Render Test',
        'type' => 'render'
    ];

    /**
     * @var array
     */
    protected $type = [
        'attribute_code' => 'type',
        'default_value' => 'render',
    ];
}

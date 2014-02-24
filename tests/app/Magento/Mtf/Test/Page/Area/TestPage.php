<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Mtf\Test\Page\Area;

use Mtf\Page\Page;

/**
 * Class TestPage
 *
 * @package Magento\Mtf\Test\Page\Area
 */
class TestPage extends Page
{
    const MCA = 'testPage';

    protected $_blocks = array(
        'testBlock' => array(
            'name' => 'testBlock',
            'class' => 'Magento\Mtf\Test\Block\TestBlock',
            'locator' => 'body',
            'strategy' => 'tag name',
        ),
    );

    /**
     * @return \Magento\Mtf\Test\Block\TestBlock
     */
    public function getTestBlock()
    {
        return $this->getBlockInstance('testBlock');
    }

    /**
     * Init page. Set page url
     */
    protected function _init()
    {
        $this->_url = 'http://google.com/';
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Mtf\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Class Test
 *
 * @package Magento\Mtf\Test\Fixture
 */
class Test extends InjectableFixture
{
    protected $repositoryClass = 'Magento\Mtf\Test\Repository\Test';

    protected $handlerInterface = 'Magento\Mtf\Test\Handler\Test\TestInterface';

    protected $defaultDataSet = [
        'locator' => null,
        'strategy' => null,
    ];

    protected $locator = [
        'attribute_code' => 'locator',
        'frontend_input' => 'text',
        'frontend_label' => 'Locator',
        'is_required' => '1',
        'default_value' => 'About',
    ];

    protected $strategy = [
        'attribute_code' => 'strategy',
        'frontend_input' => 'text',
        'frontend_label' => 'Strategy',
        'is_required' => '1',
        'default_value' => 'link text',
        'fixture' => 'Magento\Mtf\Test\Fixture\Test\Strategy',
    ];

    public function getLocator()
    {
        return $this->getData('locator');
    }

    public function getStrategy()
    {
        return $this->getData('strategy');
    }
}

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
    protected $defaultDataSet = [
        'locator' => null,
        'strategy' => null,
        'search' => null
    ];

    protected $locator = [
        'attribute_code' => 'locator',
        'frontend_input' => 'text',
        'frontend_label' => 'Locator',
        'is_required' => '1',
        'default_value' => '[name="q"]',
    ];

    protected $strategy = [
        'attribute_code' => 'strategy',
        'frontend_input' => 'text',
        'frontend_label' => 'Strategy',
        'is_required' => '1',
        'default_value' => 'css selector',
        'fixture' => 'Magento\Mtf\Test\Fixture\Test\Strategy',
    ];

    protected $search = [
        'attribute_code' => 'search',
        'default_value' => 'MTF',
    ];

    public function getLocator()
    {
        return $this->getData('locator');
    }

    public function getStrategy()
    {
        return $this->getData('strategy');
    }

    public function getSearch()
    {
        return $this->getData('search');
    }
}

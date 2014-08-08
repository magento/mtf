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
 */
class Test extends InjectableFixture
{
    /**
     * @var array
     */
    protected $defaultDataSet = [
        'locator' => null,
        'strategy' => null,
        'search' => null,
        'type' => null,
    ];

    /**
     * @var array
     */
    protected $locator = [
        'attribute_code' => 'locator',
        'frontend_input' => 'text',
        'frontend_label' => 'Locator',
        'is_required' => '1',
        'default_value' => '[name="q"]',
    ];

    /**
     * @var array
     */
    protected $strategy = [
        'attribute_code' => 'strategy',
        'frontend_input' => 'text',
        'frontend_label' => 'Strategy',
        'is_required' => '1',
        'default_value' => 'css selector',
        'fixture' => 'Magento\Mtf\Test\Fixture\Test\Strategy',
    ];

    /**
     * @var array
     */
    protected $search = [
        'attribute_code' => 'search',
        'default_value' => 'MTF',
    ];

    /**
     * @var array
     */
    protected $type = [
        'attribute_code' => 'type',
        'default_value' => 'test',
    ];

    /**
     * @return mixed
     */
    public function getLocator()
    {
        return $this->getData('locator');
    }

    /**
     * @return mixed
     */
    public function getStrategy()
    {
        return $this->getData('strategy');
    }

    /**
     * @return mixed
     */
    public function getSearch()
    {
        return $this->getData('search');
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->getData('type');
    }
}

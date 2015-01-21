<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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

<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Test\Fixture\BlockRenderMtf;

use Magento\Mtf\Fixture\FixtureInterface;

/**
 * Class Strategy.
 */
class Strategy implements FixtureInterface
{
    /**
     * Data set configuration settings.
     *
     * @var array
     */
    protected $params = [];

    /**
     * @constructor
     * @param array $data
     * @param array $params
     * @param bool $persist
     */
    public function __construct($data, array $params = [], $persist = false)
    {
        $this->data = $data;
        $this->params = $params;
        if ($persist) {
            $this->persist();
        }
    }

    /**
     * Persists prepared data into application.
     *
     * @return void
     */
    public function persist()
    {
        //
    }

    /**
     * Return prepared data set.
     *
     * @param string $key [optional]
     * @return mixed
     */
    public function getData($key = null)
    {
        return $this->data;
    }

    /**
     * Return data set configuration settings.
     *
     * @return string
     */
    public function getDataConfig()
    {
        return $this->params;
    }
}

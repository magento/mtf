<?php
/**
 * Copyright © 2017 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Test\Fixture\Test;

use Magento\Mtf\Fixture\DataSource;

/**
 * Data source for strategy field.
 */
class Strategy extends DataSource
{
    /**
     * Whether to persist field or not.
     *
     * @var bool
     */
    private $persist;

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
        $this->persist = $persist;
    }

    /**
     * Return prepared data set.
     *
     * @param string $key [optional]
     * @return mixed
     */
    public function getData($key = null)
    {
        if ($this->persist) {
            $this->persist();
        }
        return parent::getData($key);
    }
}

<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Repository;

/**
 * Class Abstract Repository
 *
 * @api
 * @abstract
 */
abstract class AbstractRepository implements RepositoryInterface
{
    /**
     * @var array
     */
    protected $_data = [];

    /**
     * @constructor
     * @param array $defaultConfig
     * @param array $defaultData
     */
    abstract public function __construct(array $defaultConfig, array $defaultData);

    /**
     * Get a Data Set from repository
     * Return all Data Sets if no data set name specified
     *
     * @param string $dataSetName
     * @return array
     */
    public function get($dataSetName = null)
    {
        if (!isset($dataSetName)) {
            return $this->_data;
        }

        return isset($this->_data[$dataSetName]) ? $this->_data[$dataSetName] : [];
    }
}

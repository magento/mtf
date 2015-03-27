<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Repository;

/**
 * Interface for Repository classes
 *
 * @api
 */
interface RepositoryInterface
{
    /**
     * @constructor
     * @param array $defaultConfig
     * @param array $defaultData
     */
    public function __construct(array $defaultConfig, array $defaultData);

    /**
     * Get a Data Set from repository
     * Return all Data Sets if no data set name specified.
     *
     * @param string $dataSetName
     * @return array
     */
    public function get($dataSetName = null);
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Repository;

/**
 * Interface for Repository classes
 *
 * @package Mtf\Repository
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
     *
     * Return all Data Sets if no data set name specified.
     *
     * @param string $dataSetName
     * @return array
     */
    public function get($dataSetName = null);
}

<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Repository;

/**
 * Class Abstract Repository
 *
 * @package Mtf\Repository
 */
abstract class AbstractRepository
{
    /**
     * @var array
     */
    protected $_data = array();

    /**
     * @param array $defaultConfig
     * @param array $defaultData
     */
    abstract public function __construct(array $defaultConfig, array $defaultData);

    /**
     * Get a Data Set from repository
     *
     * Return all Data Sets if no data set name specified.
     *
     * @param string $dataSetName
     * @return array
     */
    public function get($dataSetName = null)
    {
        if (!isset($dataSetName)) {
            return $this->_data;
        }

        return isset($this->_data[$dataSetName]) ? $this->_data[$dataSetName] : array();
    }
}

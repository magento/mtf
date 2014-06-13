<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Handler;

use Mtf\System\Config;

/**
 * Class Curl handler
 *
 * Abstract class for curl handlers
 *
 * @package Mtf\Handler
 * @api
 * @abstract
 */
abstract class Curl implements HandlerInterface
{
    /**
     * Configuration parameters array
     *
     * @var Config
     */
    protected $_configuration;

    /**
     * Mapping values for data.
     *
     * @var array
     */
    protected $mappingData;

    /**
     * Constructor
     *
     * @constructor
     * @param Config $configuration
     */
    public function __construct(Config $configuration)
    {
        $this->_configuration = $configuration;
    }

    /**
     * Replace mapping data in fixture data
     *
     * @param array $data
     * @return array
     */
    protected function replaceMappingData(array $data)
    {
        foreach ($data as $key => $value) {
            if (!isset($this->mappingData[$key])) {
                continue;
            }
            if (is_array($value)) {
                $data[$key] = $this->replaceMappingValues($value, $this->mappingData[$key]);
            } else {
                $data[$key] = isset($this->mappingData[$key][$value]) ? $this->mappingData[$key][$value] : $value;
            }
        }
        return $data;
    }

    /**
     * Replace mapping data in fixture values
     *
     * @param array $data
     * @param array $mappingData
     * @return array
     */
    private function replaceMappingValues(array $data, array $mappingData)
    {
        foreach ($data as $key => $value) {
            if (isset($mappingData[$value])) {
                $data[$key] = $mappingData[$value];
            }
        }
        return $data;
    }
}

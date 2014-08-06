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
 * Abstract class for curl handlers
 *
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
            if (is_array($value)) {
                $data[$key] = $this->replaceMappingData($value);
            } else {
                if (!isset($this->mappingData[$key])) {
                    continue;
                }
                $data[$key] = isset($this->mappingData[$key][$value]) ? $this->mappingData[$key][$value] : $value;
            }
        }

        return $data;
    }
}

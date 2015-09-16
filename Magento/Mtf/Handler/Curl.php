<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Handler;

use Magento\Mtf\Config\DataInterface;
use Magento\Mtf\System\Event\EventManagerInterface;

/**
 * Abstract class for curl handlers.
 *
 * @api
 * @abstract
 */
abstract class Curl implements HandlerInterface
{
    /**
     * Configuration parameters array.
     *
     * @var DataInterface
     */
    protected $_configuration;

    /**
     * Event Manager.
     *
     * @var EventManagerInterface
     */
    protected $_eventManager;

    /**
     * Mapping values for data.
     *
     * @var array
     */
    protected $mappingData;

    /**
     * @constructor
     * @param DataInterface $configuration
     * @param EventManagerInterface $eventManager
     */
    public function __construct(DataInterface $configuration, EventManagerInterface $eventManager)
    {
        $this->_configuration = $configuration;
        $this->_eventManager = $eventManager;
    }

    /**
     * Replace mapping data in fixture data.
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

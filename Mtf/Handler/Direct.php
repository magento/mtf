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
 * Class Direct
 *
 * Abstract class for direct handlers
 *
 * @package Mtf\Handler
 * @api
 * @abstract
 */
abstract class Direct implements HandlerInterface
{
    /**
     * Configuration parameters array
     *
     * @var Config
     */
    protected $_configuration;

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
}

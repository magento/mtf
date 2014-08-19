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
 * Class Webapi
 * Abstract class for webapi handlers
 *
 * @api
 * @abstract
 */
abstract class Webapi implements HandlerInterface
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

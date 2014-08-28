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
 * Class Ui
 *
 * Abstract class for Ui handlers
 *
 * @api
 * @abstract
 */
abstract class Ui implements HandlerInterface
{
    /**
     * Configuration
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

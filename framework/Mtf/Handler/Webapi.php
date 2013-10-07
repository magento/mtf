<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Handler;

use Mtf\Handler;
use Mtf\System\Config;

/**
 * Class Webapi
 *
 * Abstract class for webapi handlers
 *
 * @package Mtf\Handler
 */
abstract class Webapi implements Handler
{
    /**
     * Configuration parameters array
     *
     * @var Config
     */
    protected $_configuration;

    /**
     * Constructor
     * @constructor
     * @param Config $configuration
     */
    public function __construct($configuration)
    {
        $this->_configuration = $configuration;
    }
}
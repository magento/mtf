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
 * Class Curl handler
 *
 * Abstract class for curl handlers
 *
 * @package Mtf\Handler
 */
abstract class Curl implements Handler
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

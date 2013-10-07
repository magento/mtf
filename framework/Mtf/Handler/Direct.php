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
 * Class Direct
 *
 * Abstract class for direct handlers
 *
 * @package Mtf\Handler
 */
abstract class Direct implements Handler
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

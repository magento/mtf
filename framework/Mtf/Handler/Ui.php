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
 * Class Ui
 *
 * Abstract class for Ui handlers
 *
 * @package Mtf\Handler
 */
abstract class Ui implements Handler
{
    /**
     * Configuration
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

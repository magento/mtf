<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Handler;

use Magento\Mtf\Config\DataInterface;

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
     * @var DataInterface
     */
    protected $_configuration;

    /**
     * Constructor
     *
     * @constructor
     * @param DataInterface $configuration
     */
    public function __construct(DataInterface $configuration)
    {
        $this->_configuration = $configuration;
    }
}

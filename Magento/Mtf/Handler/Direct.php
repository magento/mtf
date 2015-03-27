<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Handler;

use Magento\Mtf\Config\DataInterface;

/**
 * Class Direct
 * Abstract class for direct handlers
 *
 * @api
 * @abstract
 */
abstract class Direct implements HandlerInterface
{
    /**
     * Configuration parameters array
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

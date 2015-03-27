<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\TestSuite;

use Magento\Mtf\ObjectManager;

/**
 * Class RegularTestCase
 *
 * @api
 */
class RegularTestCase extends Injectable
{
    /**
     * To accept $class argument name instead of $theClass
     *
     * @constructor
     * @param string $class
     * @param string $name
     */
    public function __construct($class = '', $name = '')
    {
        parent::__construct($class, $name);
    }
}

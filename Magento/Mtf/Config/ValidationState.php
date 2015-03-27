<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Config;

/**
 * Class ValidationState
 * Used for Object Manager.
 *
 * @internal
 */
class ValidationState implements ValidationStateInterface
{
    /**
     * @var string
     */
    protected $_appMode;

    /**
     * @constructor
     * @param string $appMode
     */
    public function __construct($appMode)
    {
        $this->_appMode = $appMode;
    }

    /**
     * Retrieve current validation state
     *
     * @return boolean
     */
    public function isValidated()
    {
        return $this->_appMode == 'developer'; // @todo
    }
}

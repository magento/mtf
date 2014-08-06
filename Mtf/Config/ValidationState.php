<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Config;

use Magento\Framework\Config\ValidationStateInterface;

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

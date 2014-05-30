<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */


namespace Mtf\System\Event\Config;

use Magento\Framework\Config\ValidationStateInterface;

class ValidationState implements ValidationStateInterface
{
    /**
     * Validate
     *
     * @return bool
     */
    public function isValidated()
    {
        return true;
    }
} 
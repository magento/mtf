<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Mtf\System\Event\Config;

use Magento\Framework\Config\ValidationStateInterface;

/**
 * Class for defining different behaviour depending on DEVELOPER mode
 */
class ValidationState implements ValidationStateInterface
{
    /**
     * Determines the behaviour on the DEVELOPER mode enabled or not; should be used as DEVELOPER mode always in MTF
     *
     * @return bool
     */
    public function isValidated()
    {
        return true;
    }
}

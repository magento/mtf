<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Exception
 * @copyright  {copyright}
 * @license    {license_link}
 */


namespace Magento;

class Exception extends \Exception
{
    /**
     * Check PCRE PREG error and throw exception
     *
     * @throws \Magento\Exception
     */
    public static function processPcreError()
    {
        if (preg_last_error() != PREG_NO_ERROR) {
            switch (preg_last_error()) {
                case PREG_INTERNAL_ERROR:
                    throw new \Magento\Exception('PCRE PREG internal error');
                case PREG_BACKTRACK_LIMIT_ERROR:
                    throw new \Magento\Exception('PCRE PREG Backtrack limit error');
                case PREG_RECURSION_LIMIT_ERROR:
                    throw new \Magento\Exception('PCRE PREG Recursion limit error');
                case PREG_BAD_UTF8_ERROR:
                    throw new \Magento\Exception('PCRE PREG Bad UTF-8 error');
                case PREG_BAD_UTF8_OFFSET_ERROR:
                    throw new \Magento\Exception('PCRE PREG Bad UTF-8 offset error');
            }
        }
    }
}

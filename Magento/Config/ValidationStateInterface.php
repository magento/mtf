<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Config;

interface ValidationStateInterface
{
    /**
     * Retrieve current validation state
     *
     * @return boolean
     */
    public function isValidated();
}

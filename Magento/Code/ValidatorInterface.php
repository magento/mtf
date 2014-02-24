<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Code;

interface ValidatorInterface
{
    /**
     * Validate class
     *
     * @param string $className
     * @return bool
     * @throws \Magento\Code\ValidationException
     */
    public function validate($className);
} 
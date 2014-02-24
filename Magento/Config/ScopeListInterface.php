<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Config;

interface ScopeListInterface
{
    /**
     * Retrieve list of all scopes
     *
     * @return array
     */
    public function getAllScopes();
}

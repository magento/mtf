<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\ObjectManager;

use Magento\ObjectManager\Config\Config as ObjectManagerConfig;

/**
 * Class Config
 * Filesystem configuration loader. Loads configuration from XML files, split by scopes
 *
 * @package Mtf\ObjectManager
 * @internal
 */
class Config extends ObjectManagerConfig
{
    /**
     * @var \ReflectionClass[]
     */
    protected $_nonSharedRefClasses = [];

    /**
     * Check whether type is shared
     *
     * @param string $type
     * @return bool
     */
    public function isShared($type)
    {
        if (isset($this->_nonShared[$type])) {
            return false;
        }

        if (!isset($this->_nonSharedRefClasses[$type])) {
            $this->_nonSharedRefClasses[$type] = new \ReflectionClass($type);
        }
        foreach ($this->_nonShared as $noneShared => $flag) {
            if ($this->_nonSharedRefClasses[$type]->isSubclassOf($noneShared)) {
                return false;
            }
        }

        return true;
    }
}

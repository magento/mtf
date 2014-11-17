<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mtf\ObjectManager;

use Magento\Framework\ObjectManager\Config\Config as ObjectManagerConfig;

/**
 * Class Config
 * Filesystem configuration loader. Loads configuration from XML files, split by scopes
 *
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

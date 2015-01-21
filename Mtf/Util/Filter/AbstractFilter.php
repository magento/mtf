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

namespace Mtf\Util\Filter;

/**
 * Base class filters out classes that are affected by some parameters.
 */
abstract class AbstractFilter extends \Mtf\Util\CrossModuleReference\Common
{
    /**
     * List of allow parameters.
     *
     * @var array
     */
    protected $allow;

    /**
     * List of deny parameters.
     *
     * @var array
     */
    protected $deny;

    /**
     * @constructor
     * @param \Mtf\Config\DataInterface $configData
     * @param string $pathConfig
     */
    public function __construct(
        \Mtf\Config\DataInterface $configData,
        $pathConfig
    ) {
        $this->allow = $configData->get($pathConfig . '/allow');
        if (!$this->allow) {
            $this->allow = [];
        }

        $this->deny = $configData->get($pathConfig . '/deny');
        if (!$this->deny) {
            $this->deny = [];
        }
    }
}

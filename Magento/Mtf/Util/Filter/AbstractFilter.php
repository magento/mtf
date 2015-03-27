<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Util\Filter;

use Magento\Mtf\Config\DataInterface;

/**
 * Base class filters out classes that are affected by some parameters.
 */
abstract class AbstractFilter extends \Magento\Mtf\Util\CrossModuleReference\Common
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
     * @param DataInterface $configData
     * @param string $scope
     * @param string $type
     */
    public function __construct(DataInterface $configData, $scope, $type)
    {
        $this->allow = $configData->get('rule/' . $scope . '/allow/0/' . $type);
        if (!$this->allow) {
            $this->allow = [];
        }

        $this->deny = $configData->get('rule/' . $scope . '/deny/0/' . $type);
        if (!$this->deny) {
            $this->deny = [];
        }
    }
}

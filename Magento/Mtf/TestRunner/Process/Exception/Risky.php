<?php
/**
 * Copyright © 2017 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\TestRunner\Process\Exception;

/**
 * Serializable exception for parallel run.
 */
class Risky extends \PHPUnit_Framework_RiskyTestError
{
    /**
     * Returns available object values.
     *
     * @return array
     */
    public function __sleep()
    {
        return ['message', 'serializableTrace'];
    }
}

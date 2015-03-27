<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\TestRunner\Process\Exception;

/**
 * Serializable exception for parallel run
 */
class Skipped extends \PHPUnit_Framework_SkippedTestError
{
    /**
     * @return array
     */
    public function __sleep()
    {
        return ['message'];
    }
}

<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Util\CrossModuleReference;

/**
 * Interface for cross module reference checker
 */
interface CheckerInterface
{
    /**
     * Return a list of testcases that cross reference artifact in the specified module
     * The moduleName should be in the form of Prefix_Shortname, e.g., Magento_Checkout
     * The returned array has test case class name as array keys.
     *
     * @param string $moduleName
     * @return array
     */
    public function getCrossModuleReference($moduleName);
}

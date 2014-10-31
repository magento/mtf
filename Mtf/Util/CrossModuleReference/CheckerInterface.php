<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Util\CrossModuleReference;

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
     * @param $moduleName
     * @return array
     */
    public function getCrossModuleReference($moduleName);
}

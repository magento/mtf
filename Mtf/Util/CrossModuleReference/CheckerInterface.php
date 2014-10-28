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
     *
     * @param $moduleName
     * @return array
     */
    public function getCrossModuleReference($moduleName);
}

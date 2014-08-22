<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\TestStep;

/**
 * Interface TestStepInterface
 * Interface for TestCase Scenario Step classes
 */
interface TestStepInterface
{
    /**
     * Run step flow
     *
     * @return mixed
     */
    public function run();
}

<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\TestStep;

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

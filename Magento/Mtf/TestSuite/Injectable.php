<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\TestSuite;

/**
 * Class Functional
 * Test suite to handle parallel run process completion for injectable tests
 */
class Injectable extends \Magento\Mtf\TestSuite\TestSuite
{
    /**
     * Wait for parallel processes to complete (for parallel run)
     * @return void
     */
    protected function waitForProcessesToComplete()
    {
        if ($this instanceof \Magento\Mtf\TestSuite\TestCase) {
            parent::waitForProcessesToComplete();
        }
    }
}

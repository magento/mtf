<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\Config;

/**
 * Interface ValidationStateInterface
 */
interface ValidationStateInterface
{
    /**
     * Retrieve current validation state
     *
     * @return boolean
     */
    public function isValidated();
}

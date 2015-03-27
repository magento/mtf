<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Block;

/**
 * Interface for Blocks
 *
 * @api
 */
interface BlockInterface
{
    /**
     * Check if the root element of the block is visible or not
     *
     * @return boolean
     */
    public function isVisible();
}

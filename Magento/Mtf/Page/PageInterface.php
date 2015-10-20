<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Page;

use Magento\Mtf\Block\BlockInterface;

/**
 * Interface for Pages.
 *
 * @api
 */
interface PageInterface
{
    /**
     * Open the page URL in browser.
     *
     * @param array $params [optional]
     * @return $this
     */
    public function open(array $params = []);

    /**
     * Retrieve an instance of block.
     *
     * @param string $blockName
     * @return BlockInterface
     */
    public function getBlockInstance($blockName);
}

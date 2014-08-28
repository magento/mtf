<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Block;

use Mtf\Client\Driver\Selenium\Element;

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

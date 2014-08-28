<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf;

use Mtf\Client\Driver\Selenium\Element;

/**
 * Interface for Block classes
 */
interface Block
{
    /**
     * Check if the root element of the block is visible or not
     *
     * @return boolean
     */
    public function isVisible();
}

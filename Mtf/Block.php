<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf;

use Mtf\Client\Driver\Selenium\Element;

/**
 * Interface for Block classes
 *
 * @package Mtf
 */
interface Block
{
    /**
     * Check if the root element of the block is visible or not
     * @return boolean
     */
    public function isVisible();
}

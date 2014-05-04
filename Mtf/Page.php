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

/**
 * Interface for Pages
 *
 * @package Mtf
 */
interface Page
{
    /**
     * Open the page URL in browser.
     *
     * @param array $params [optional]
     * @return void
     */
    public function open(array $params = array());
}

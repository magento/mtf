<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf;

/**
 * Interface for Pages
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

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Page;

/**
 * Class BackendPage
 *
 * @api
 */
class BackendPage extends Page
{
    /**
     * Init page. Set page url
     * @return void
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . static::MCA;
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Page;

/**
 * Class FrontendPage
 *
 * @package Mtf\Page
 * @api
 */
class FrontendPage extends Page
{
    /**
     * Init page. Set page url
     * @return void
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . static::MCA;
    }
}

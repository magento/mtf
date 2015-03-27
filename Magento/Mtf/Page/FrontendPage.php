<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Page;

/**
 * Class FrontendPage
 *
 * @api
 */
class FrontendPage extends Page
{
    /**
     * Init page. Set page url
     *
     * @return void
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . static::MCA;
    }
}

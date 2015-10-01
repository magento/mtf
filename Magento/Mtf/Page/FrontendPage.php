<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Page;

/**
 * Application frontend page.
 *
 * @api
 */
class FrontendPage extends Page
{
    /**
     * Init page. Set page url.
     *
     * @return void
     */
    protected function initUrl()
    {
        $this->url = $_ENV['app_frontend_url'] . static::MCA;
    }
}

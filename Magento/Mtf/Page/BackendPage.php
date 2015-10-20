<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Page;

/**
 * Application backend page.
 *
 * @api
 */
class BackendPage extends Page
{
    /**
     * Init page. Set page url.
     *
     * @return void
     */
    protected function initUrl()
    {
        $this->url = $_ENV['app_backend_url'] . static::MCA;
    }
}

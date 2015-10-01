<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Page;

/**
 * Page for external solutions.
 */
class ExternalPage extends Page
{
    /**
     * Init page. Set page url.
     *
     * @return void
     */
    protected function initUrl()
    {
        $this->url = static::MCA;
    }
}

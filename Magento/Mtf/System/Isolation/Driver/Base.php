<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\System\Isolation\Driver;

use Magento\Mtf\System\Isolation\Driver;

/**
 * Class Base
 * Base isolation driver
 *
 * @internal
 */
class Base implements Driver
{
    /**
     * Reset url
     *
     * @var string
     */
    private $_resetUrl;

    /**
     * Constructor
     *
     * @constructor
     */
    public function __construct()
    {
        $config = \Magento\Mtf\ObjectManager::getInstance()->get('Magento\Mtf\Config\GlobalConfig');
        $this->_resetUrl = $_ENV['app_frontend_url'] . $config->get('isolation/0/reset_url_path/0/value');
    }

    /**
     * Isolation by calling resetUrl
     *
     * @return void
     */
    public function isolate()
    {
        file_get_contents($this->_resetUrl);
    }
}

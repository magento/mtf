<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mtf\System\Isolation\Driver;

use Mtf\System\Isolation\Driver;
use Mtf\Config; // Mtf\SystemConfig

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
        $config = \Mtf\ObjectManager::getInstance()->get('Mtf\Config\GlobalConfig');
        $this->_resetUrl = $_ENV['app_frontend_url'] . $config->get('isolation/reset_url_path');
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

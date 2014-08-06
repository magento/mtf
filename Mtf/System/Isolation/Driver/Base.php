<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\System\Isolation\Driver;

use Mtf\System\Isolation\Driver;
use Mtf\System\Config;

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
        $config = new Config();
        $this->_resetUrl = $_ENV['app_frontend_url'] . $config->getConfigParam('isolation/reset_url_path');
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

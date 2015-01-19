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

namespace Mtf\Client\Driver\Selenium;

use Mtf\Config;

/**
 * Class RemoteDriver
 * Entry point to selenium
 *
 * @api
 */
class RemoteDriver extends \PHPUnit_Extensions_Selenium2TestCase
{
    /**
     * Timeout for wait until
     *
     * @var int
     */
    protected $timeout;

    /**
     * Constructor
     *
     * @param \Mtf\Config\Data $config
     */
    public function __construct(\Mtf\Config\Data $config)
    {
        $this->timeout = $config->get('server/selenium/seleniumServerRequestsTimeout', 10) * 1000;
    }

    /**
     * Wait until callback isn't null or timeout occurs
     *
     * @param callable $callback
     * @param null|int $timeout
     * @return mixed
     */
    public function waitUntil($callback, $timeout = null)
    {
        $timeout = $this->timeout !== null ? $this->timeout : $timeout;
        return \Mtf\Client\Driver\Selenium\WaitUntil::run($callback, $this, $timeout);
    }

    /**
     * Force set new session strategy. This will lead to clear all previous data stored in session
     *
     * @param string $strategy
     * @return void
     */
    public function setSessionStrategy($strategy)
    {
        $this->setUpSessionStrategy(['sessionStrategy' => $strategy]);
    }

    /**
     * Avoid serialization of closure
     * 
     * @return array
     */
    public function __sleep()
    {
        return [];
    }
}

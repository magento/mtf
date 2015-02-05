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

namespace Magento\Mtf\Client\Driver\Facebook;

use Magento\Mtf\ObjectManager;
use Magento\Mtf\Config\Data;
use Magento\Mtf\System\Event\EventManagerInterface;

/**
 * Class RemoteDriver
 */
final class RemoteDriver extends \RemoteWebDriver
{
    /**
     * Default connection timeout
     */
    const DEFAULT_CONNECTION_TIMEOUT = 50;

    /**
     * Default url
     */
    const URL_TEMPLATE = 'http://{host}:{port}/wd/hub';

    /**
     * Event Manager instance
     *
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * Timeout for wait until
     *
     * @var int
     */
    protected $timeout;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var int
     */
    protected $connectionTimeoutInMs;

    /**
     * @var array
     */
    protected $desiredCapabilities;

    /**
     * Constructor
     *
     * @param Data $config
     * @param EventManagerInterface $eventManager
     * @param ObjectManager $objectManager
     */
    public function __construct(Data $config, EventManagerInterface $eventManager, ObjectManager $objectManager)
    {
        $this->eventManager = $eventManager;
        $this->objectManager = $objectManager;

        $url = strtr(
            static::URL_TEMPLATE,
            [
                '{host}' => $config->get(
                    'server/selenium/host',
                    'localhost'
                ),
                '{port}' => $config->get(
                    'server/selenium/port',
                    4444
                )
            ]
        );
        $this->url = rtrim($url, '/');

        $this->desiredCapabilities = $this->getDesiredCapabilities(
            $config->get('server/selenium/browserName')
        );
        if ($this->desiredCapabilities instanceof \DesiredCapabilities) {
            $this->desiredCapabilities = $this->desiredCapabilities->toArray();
        }

        $this->connectionTimeoutInMs = $config->get(
            'server/selenium/seleniumServerRequestsTimeout',
            static::DEFAULT_CONNECTION_TIMEOUT
        );

        $this->connectionTimeoutInMs *= 1000;

        $this->init();
    }

    /**
     * Driver init
     *
     * @param null $sessionId
     * @throws \WebDriverException
     * @return void
     */
    protected function init($sessionId = null)
    {
        /** @var \HttpCommandExecutor $executor */
        $executor = $this->objectManager->create('HttpCommandExecutor', ['url' => $this->url]);
        $executor->setConnectionTimeout($this->connectionTimeoutInMs);

        /** @var \WebDriverCommand $command */
        $command = $this->objectManager->create(
            'WebDriverCommand',
            [
                'session_id' => $sessionId,
                'name' => \DriverCommand::NEW_SESSION,
                'parameters' => ['desiredCapabilities' => $this->desiredCapabilities]
            ]
        );

        $this->setSessionID($executor->execute($command)->getSessionID())->setCommandExecutor($executor);
    }

    /**
     * Create new session
     *
     * @param null $sessionId
     * @return void
     */
    public function createNewSession($sessionId = null) {
        $this->init($sessionId);
    }

    /**
     * Get desired capabilities browser
     *
     * @param string $browserName
     * @return \DesiredCapabilities
     */
    protected function getDesiredCapabilities($browserName)
    {
        switch ($browserName) {
            case 'chrome':
                return \DesiredCapabilities::chrome();
            case 'firefox':
            default:
                return \DesiredCapabilities::firefox();
        }
    }
}

<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Client\Driver\Facebook;

use Magento\Mtf\ObjectManager;
use Magento\Mtf\Config\DataInterface;
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
     * @param DataInterface $config
     * @param EventManagerInterface $eventManager
     * @param ObjectManager $objectManager
     */
    public function __construct(
        DataInterface $config,
        EventManagerInterface $eventManager,
        ObjectManager $objectManager
    ) {
        $this->eventManager = $eventManager;
        $this->objectManager = $objectManager;

        $url = strtr(
            static::URL_TEMPLATE,
            [
                '{host}' => $config->get(
                    'server/0/item/selenium/host',
                    'localhost'
                ),
                '{port}' => $config->get(
                    'server/0/item/selenium/port',
                    4444
                )
            ]
        );
        $this->url = rtrim($url, '/');

        $this->desiredCapabilities = $this->getDesiredCapabilities(
            $config->get('server/0/item/selenium/browserName')
        );
        if ($this->desiredCapabilities instanceof \DesiredCapabilities) {
            $this->desiredCapabilities = $this->desiredCapabilities->toArray();
        }

        $this->connectionTimeoutInMs = $config->get(
            'server/0/item/selenium/seleniumServerRequestsTimeout',
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

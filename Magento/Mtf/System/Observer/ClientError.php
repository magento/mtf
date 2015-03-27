<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\System\Observer;

use Magento\Mtf\System\Logger;
use Magento\Mtf\System\Event\Event;
use Magento\Mtf\Client\BrowserInterface;
use Magento\Mtf\System\Event\State as EventState;

/**
 * Class ClientError
 */
class ClientError extends AbstractObserver
{
    /**
     * Log file name
     */
    const FILE_NAME = 'client_error.log';

    /**
     * Filename of the log file
     *
     * @var string
     */
    protected $filename;

    /**
     * @param Logger $logger
     * @param EventState $state
     * @param BrowserInterface $browser
     * @param string $filename
     */
    public function __construct(Logger $logger, EventState $state, BrowserInterface $browser, $filename = null)
    {
        parent::__construct($logger, $state);
        $this->browser = $browser;
        $this->filename = $filename ?: static::FILE_NAME;
    }

    /**
     * Process current event
     *
     * @param Event $event
     * @return void
     */
    public function process(Event $event)
    {
        try {
            $errors = $this->browser->getJsErrors();
        } catch (\Exception $exception) {
            $this->logger->log("Unable to get Js Errors. Exception: \n" . $exception . "\n", $this->filename);
        }
        if (!empty($errors)) {
            $this->logger->log($this->getMessagePrefix($event) . "\n", $this->filename);
            foreach ($errors as $url => $jsErrors) {
                $this->logger->log($url . "\n", $this->filename);
                foreach ($jsErrors as $error) {
                    $this->logger->log($error . "\n", $this->filename);
                }
            }
        }
        $this->browser->injectJsErrorCollector();
    }
}

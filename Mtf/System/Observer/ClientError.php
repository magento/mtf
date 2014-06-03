<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Mtf\System\Observer;

use Mtf\System\Logger;
use Mtf\System\Event\State;
use Mtf\System\Event\Event;
use \Mtf\Client\Driver\Selenium\Browser;

class ClientError implements \Mtf\System\Event\ObserverInterface
{
    /**
     * Log file name
     */
    const FILE_NAME = 'client_error.log';

    /**
     * Logger class
     *
     * @var Logger
     */
    protected $logger;

    /**
     * @var State
     */
    protected $state;

    /**
     * Filename of the log file
     *
     * @var string
     */
    protected $filename;

    /**
     * @param Logger $logger
     * @param State $state
     * @param Browser $browser
     * @param string $filename
     */
    public function __construct(Logger $logger, State $state, Browser $browser, $filename = null)
    {
        $this->logger = $logger;
        $this->state = $state;
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
        $currentUrl = $this->browser->getUrl();
        if ($this->state->getPageUrl() != $currentUrl) {
            $this->logger->log($event->getMessagePrefix(), $this->filename);
            foreach ($this->browser->getJsErrors() as $url => $jsErrors) {
                $this->logger->log($url, $this->filename);
                foreach ($jsErrors as $error) {
                    $this->logger->log($error, $this->filename);
                }
            }
            $this->browser->injectJsErrorCollector();
        }
    }
}

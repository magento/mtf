<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Mtf\System\Observer;

use Mtf\System\Logger;
use Mtf\System\Event\State as EventState;
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
     * @var EventState
     */
    protected $state;

    /**
     * Filename of the log file
     *
     * @var string
     */
    protected $filename;

    /**
     * @var bool
     */
    private static $injected = false;

    /**
     * @param Logger $logger
     * @param EventState $state
     * @param Browser $browser
     * @param string $filename
     */
    public function __construct(Logger $logger, EventState $state, Browser $browser, $filename = null)
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
        if (!self::$injected) {
            $this->browser->injectJsErrorCollector();
            self::$injected = true;
            return;
        }
        $this->logger->log($event->getMessagePrefix() . "\n", $this->filename);
        $errors = $this->browser->getJsErrors();
        if (!empty($errors)) {
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

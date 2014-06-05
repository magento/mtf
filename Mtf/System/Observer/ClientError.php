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
     * @param Browser $browser
     * @param string $filename
     */
    public function __construct(Logger $logger, EventState $state, Browser $browser, $filename = null)
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
        $errors = $this->browser->getJsErrors();
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

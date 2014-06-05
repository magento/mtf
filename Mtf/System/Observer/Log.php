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

/**
 * Class for logging events in MTF
 */
class Log extends AbstractObserver
{
    /**
     * Log file name
     */
    const FILE_NAME = 'logger.log';

    /**
     * Filename of the log file
     *
     * @var string
     */
    protected $filename;

    /**
     * Constructor
     *
     * @param Logger $logger
     * @param State $state
     * @param string $filename
     */
    public function __construct(Logger $logger, State $state, $filename = null)
    {
        parent::__construct($logger, $state);
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
        foreach ($event->getSubjects() as $message) {
            $this->logger->log($this->getMessagePrefix($event) . ' ' . $message . PHP_EOL, $this->filename);
        }
    }
}

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
class Log implements \Mtf\System\Event\ObserverInterface
{
    /**
     * Log file name
     */
    const FILE_NAME = 'logger.log';

    /**
     * Logger class
     *
     * @var Logger
     */
    protected $logger;

    /**
     * filename of the log file
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
        $this->logger = $logger;
        if (!$filename) {
            $filename = static::FILE_NAME;
        }
        $this->filename = $filename;
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
            $this->logger->log($event->getIdentifier() . ' ' . $message . PHP_EOL, $this->filename);
        }
    }
}

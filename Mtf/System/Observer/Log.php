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

class Log implements \Mtf\System\Event\ObserverInterface
{
    /**
     * Log file name
     */
    const FILE_NAME = 'logger.log';

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var string
     */
    protected $filename;

    /**
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
     * @param Event $event
     *
     * @return void
     */
    public function process(Event $event)
    {
        foreach ($event->getSubjects() as $message) {
            $this->logger->log($event->getIdentifier() . ' ' . $message . PHP_EOL, $this->filename);
        }
    }
}

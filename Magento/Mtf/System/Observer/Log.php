<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\System\Observer;

use Magento\Mtf\System\Logger;
use Magento\Mtf\System\Event\State as EventState;
use Magento\Mtf\System\Event\Event;

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
     * @param EventState $state
     * @param string $filename
     */
    public function __construct(Logger $logger, EventState $state, $filename = null)
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

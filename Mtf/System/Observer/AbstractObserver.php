<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Mtf\System\Observer;

use Mtf\System\Event\ObserverInterface;
use Mtf\System\Event\Event;
use Mtf\System\Logger;
use Mtf\System\Event\State as EventState;

abstract class AbstractObserver implements ObserverInterface
{
    /**
     * @var \Mtf\System\Logger
     */
    protected $logger;

    /**
     * @var EventState
     */
    protected $state;

    /**
     * @param Logger $logger
     * @param EventState $state
     */
    public function __construct(Logger $logger, EventState $state)
    {
        $this->logger = $logger;
        $this->state = $state;
    }



    /**
     * Create directories if not exists
     *
     * @param string $suffix
     * @return string
     */
    protected function createDestinationDirectory($suffix = '')
    {
        $directory = sprintf('%s/%s/%s/' . $suffix,
            strtolower(str_replace('\\', '-', EventState::getTestSuiteName())),
            strtolower(str_replace('\\', '-', EventState::getTestClassName())),
            EventState::getTestMethodName()
        );
        if (!is_dir($this->logger->getLogDirectoryPath() . '/' . $directory)) {
            mkdir($this->logger->getLogDirectoryPath() . '/' . $directory, 0777, true);
        }
        return $directory;
    }

    /**
     * Retrieve message context prefix
     *
     * @param Event $event
     * @return string
     */
    public function getMessagePrefix(Event $event)
    {
        return sprintf(
            '%s %s %s %s %s %s %s',
            date("Y-m-d H:i:sP"),
            $event->getIdentifier(),
            $this->state->getAppStateName(),
            EventState::getTestSuiteName(),
            EventState::getTestClassName(),
            EventState::getTestMethodName(),
            $this->state->getPageUrl()
        );
    }
}

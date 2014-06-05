<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Mtf\System\Observer;

use Mtf\System\Event\State as EventState;
use Mtf\System\Event\Event;
use Mtf\System\Event\ObserverInterface;
use Mtf\System\Logger;

class Screenshot implements ObserverInterface
{
    /**
     * Image extension
     */
    const FILE_EXTENSION = '.png';

    /**
     * @var \Mtf\System\Logger
     */
    protected $logger;

    /**
     * @var \Mtf\System\Event\State
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

     /*
     * @param Event $event
     * @return void
     */
    public function process(Event $event)
    {
        $testCase = $event->getSubjects()[1];
        $this->logger->log(
            $testCase->currentScreenshot(),
            $this->createDestinationDirectory() . '/' . $event->getIdentifier() . self::FILE_EXTENSION
        );
    }

    /**
     * @return string
     */
    protected function createDestinationDirectory()
    {
        $directory = sprintf('%s/%s/%s/screenshots',
            strtolower(str_replace('\\', '-', $this->state->getTestSuiteName())),
            strtolower(str_replace('\\', '-', $this->state->getTestClassName())),
            $this->state->getTestMethodName()
        );
        if (!is_dir($this->logger->getLogDirectoryPath() . '/' . $directory)) {
            mkdir($this->logger->getLogDirectoryPath() . '/' . $directory, 0777, true);
        }
        return $directory;
    }
}

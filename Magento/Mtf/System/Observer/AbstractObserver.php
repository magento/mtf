<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\System\Observer;

use Magento\Mtf\System\Event\ObserverInterface;
use Magento\Mtf\System\Event\Event;
use Magento\Mtf\System\Logger;
use Magento\Mtf\System\Event\State as EventState;

/**
 * Class AbstractObserver
 */
abstract class AbstractObserver implements ObserverInterface
{
    /**
     * @var \Magento\Mtf\System\Logger
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
        $testClass = str_replace('\\', '-', EventState::getTestClassName());
        preg_match('`^\w*?-(\w*?)-.*?-(\w*?)$`', $testClass, $path);

        $testSuite = (preg_match('`\\\`', EventState::getTestSuiteName()) == false)
            ? EventState::getTestSuiteName()
            : 'magento';

        $testClass = isset($path[1]) ? $path[1] : 'undefined';
        $testMethod = isset($path[2]) ? $path[2] : 'undefined';

        $directory = sprintf(
            '%s/%s/%s/%s/' . $suffix,
            $testSuite,
            $testClass,
            $testMethod,
            EventState::getTestMethodName()
        );

        $dir = $this->logger->getLogDirectoryPath() . '/' . $directory;
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
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
            $this->state->getStageName(),
            $this->state->getPageUrl()
        );
    }
}

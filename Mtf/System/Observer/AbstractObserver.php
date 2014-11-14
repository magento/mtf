<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Mtf\System\Observer;

use Mtf\System\Event\ObserverInterface;
use Mtf\System\Event\Event;
use Mtf\System\Logger;
use Mtf\System\Event\State as EventState;

/**
 * Class AbstractObserver
 */
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
        $directory = sprintf(
            '%s/%s/%s/' . $suffix,
            strtolower(str_replace('\\', '-', EventState::getTestSuiteName())),
            strtolower(str_replace('\\', '-', EventState::getTestClassName())),
            EventState::getTestMethodName()
        );
        if (!is_dir($this->logger->getLogDirectoryPath() . '/' . $directory)) {
            mkdir(MTF_BP . '/' . $this->logger->getLogDirectoryPath() . '/' . $directory, 0777, true);
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

<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\System\Observer;

use Magento\Mtf\System\Event\Event;
use Magento\Mtf\System\Event\State as EventState;
use Magento\Mtf\System\Event\ObserverInterface;

/**
 * Class Fixture
 */
class Fixture implements ObserverInterface
{
    /**
     * @var EventState
     */
    protected $stateObject;

    /**
     * @param EventState $state
     */
    public function __construct(EventState $state)
    {
        $this->stateObject = $state;
    }

    /**
     * @param Event $event
     * @return void
     */
    public function process(Event $event)
    {
        if (in_array('persist_before', $event->getTags())) {
            $this->stateObject->setStageName($event->getSubjects()[0]);
        } elseif (in_array('persist_before', $event->getTags())) {
            $this->stateObject->setStageName();
        }
    }
}

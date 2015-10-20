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
 * Fixture observer.
 */
class Fixture implements ObserverInterface
{
    /**
     * EventState instance.
     *
     * @var EventState
     */
    protected $stateObject;

    /**
     * @constructor
     * @param EventState $state
     */
    public function __construct(EventState $state)
    {
        $this->stateObject = $state;
    }

    /**
     * Process current event.
     *
     * @param Event $event
     * @return void
     */
    public function process(Event $event)
    {
        if (in_array('persist_before', $event->getTags())) {
            $this->stateObject->setStageName($event->getSubjects()[0]);
        } elseif (in_array('persist_after', $event->getTags())) {
            $this->stateObject->setStageName();
        }
    }
}

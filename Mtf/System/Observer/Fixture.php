<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Mtf\System\Observer;

use Mtf\System\Event\Event;
use Mtf\System\Event\State as EventState;
use Mtf\System\Event\ObserverInterface;

/**
 * Class Fixture
 * @package Mtf\System\Observer
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

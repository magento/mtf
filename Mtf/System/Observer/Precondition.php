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

class Precondition implements ObserverInterface
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
        $condition = $event->getSubjects()[0];
        $fixtureClass = $event->getSubjects()[1];
        if ($condition == 'before') {
            $this->stateObject->startFixturePersist($fixtureClass);
        } else if ($condition == 'after') {
            $this->stateObject->stopFixturePersist($fixtureClass);
        }
    }
}

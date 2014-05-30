<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Mtf\System\Observer;

use Mtf\System\Event\Event;
use Mtf\System\Event\State;
use Mtf\System\Event\ObserverInterface;

class Precondition implements ObserverInterface
{
    /**
     * @var \Mtf\System\Event\State|State
     */
    protected $stateObject;

    /**
     * @param State $state
     */
    public function __construct(State $state)
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

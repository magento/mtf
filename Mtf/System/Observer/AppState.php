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
use Mtf\Client\Driver\Selenium\Browser;

/**
 * Class AppState
 */
class AppState implements ObserverInterface
{
    /**
     * @var \Mtf\Client\Driver\Selenium\Browser
     */
    protected $browser;

    /**
     * @var EventState
     */
    protected $stateObject;

    /**
     * @param Browser $browser
     * @param EventState $state
     */
    public function __construct(Browser $browser, EventState $state)
    {
        $this->browser = $browser;
        $this->stateObject = $state;
    }

    /**
     * @param Event $event
     * @return void
     */
    public function process(Event $event)
    {
        if (isset($event->getSubjects()[0])) {
            $this->stateObject->setAppStateName($event->getSubjects()[0]);
        }
    }
}

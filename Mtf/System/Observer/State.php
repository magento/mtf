<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Mtf\System\Observer;

use Mtf\System\Event\Event;
use Mtf\System\Event\State as EventSate;
use Mtf\System\Event\ObserverInterface;
use Mtf\Client\Driver\Selenium\Browser;

class State implements ObserverInterface
{
    /**
     * @var \Mtf\Client\Driver\Selenium\Browser
     */
    protected $browser;

    /**
     * @var \Mtf\System\Event\State|State
     */
    protected $stateObject;

    /**
     * @param Browser $browser
     * @param EventSate $state
     */
    function __construct(Browser $browser, EventSate $state)
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
        $this->stateObject->setPageUrl($this->browser->getUrl());
    }
}

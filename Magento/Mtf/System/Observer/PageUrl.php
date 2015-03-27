<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\System\Observer;

use Magento\Mtf\System\Event\Event;
use Magento\Mtf\Client\BrowserInterface;
use Magento\Mtf\System\Event\ObserverInterface;
use Magento\Mtf\System\Event\State as EventState;

/**
 * Class PageUrl
 */
class PageUrl implements ObserverInterface
{
    /**
     * @var \Magento\Mtf\Client\BrowserInterface
     */
    protected $browser;

    /**
     * @var EventState
     */
    protected $stateObject;

    /**
     * @param BrowserInterface $browser
     * @param EventState $state
     */
    public function __construct(BrowserInterface $browser, EventState $state)
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

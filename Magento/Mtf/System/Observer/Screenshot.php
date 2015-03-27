<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\System\Observer;

use Magento\Mtf\System\Logger;
use Magento\Mtf\System\Event\Event;
use Magento\Mtf\Client\BrowserInterface;
use Magento\Mtf\System\Event\State as EventState;

/**
 * Create screnshot Observer
 */
class Screenshot extends AbstractObserver
{
    /**
     * Image extension
     */
    const FILE_EXTENSION = '.png';

    /**
     * @var \Magento\Mtf\Client\BrowserInterface
     */
    protected $browser;

    /**
     * @param Logger $logger
     * @param EventState $state
     * @param BrowserInterface $browser
     */
    public function __construct(Logger $logger, EventState $state, BrowserInterface $browser)
    {
        parent::__construct($logger, $state);
        $this->browser = $browser;
    }

    /**
     * @param Event $event
     * @return void
     */
    public function process(Event $event)
    {
        $this->logger->log(
            $this->browser->getScreenshotData(),
            $this->createDestinationDirectory('screenshots') . '/' . $event->getIdentifier() . self::FILE_EXTENSION
        );
    }
}

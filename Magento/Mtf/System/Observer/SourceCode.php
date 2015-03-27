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
 * Observer for obtaining html source of the current page
 */
class SourceCode extends AbstractObserver
{
    /**
     * File name of source code
     */
    const FILE_NAME = 'source_code.log';

    /**
     * Browser object
     *
     * @var BrowserInterface
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
     * Collect page source artifact to storage
     *
     * @param Event $event
     * @return void
     */
    public function process(Event $event)
    {
        $directory = $this->createDestinationDirectory('page-source');
        $this->logger->log($this->browser->getHtmlSource(), $directory . '/' . $event->getIdentifier() . '.html');
    }
}

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
class CurlResponse extends AbstractObserver
{
    /**
     * File name of source code
     */
    const FILE_NAME = 'curl_response.log';

    /**
     * Collect page source artifact to storage
     *
     * @param Event $event
     * @return void
     */
    public function process(Event $event)
    {
        $directory = $this->createDestinationDirectory('curl-response');
        $this->logger->log($event->getSubjects()[0], $directory . '/' . $event->getIdentifier() . '.html');
    }
}

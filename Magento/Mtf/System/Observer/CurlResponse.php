<?php
/**
 * Copyright © 2017 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\System\Observer;

use Magento\Mtf\System\Event\Event;

/**
 * Observer for processing curl response.
 */
class CurlResponse extends AbstractObserver
{
    /**
     * File name of source code.
     */
    const FILE_NAME = 'curl_response.log';

    /**
     * Collect curl response artifact to storage.
     *
     * @param Event $event
     * @return void
     */
    public function process(Event $event)
    {
        $directory = $this->createDestinationDirectory('curl-response');
        $this->logger->log($event->getSubjects()[0], $directory . '/' . $event->getFileIdentifier() . '.html');
    }
}

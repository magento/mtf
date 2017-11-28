<?php
/**
 * Copyright © 2017 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\System\Observer;

use Magento\Mtf\System\Event\Event;

/**
 * AllureCurlResponse observer.
 */
class AllureCurlResponse extends AbstractAllureObserver
{
    /**
     * Collect curl response artifact to storage.
     *
     * @param Event $event
     * @return void
     */
    public function process(Event $event)
    {
        $this->addAttachment(
            $event->getSubjects()[0],
            'curl-response-' . $event->getFileIdentifier(),
            'text/html'
        );
    }
}

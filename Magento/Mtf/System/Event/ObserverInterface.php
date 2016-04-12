<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\System\Event;

/**
 * Interface ObserverInterface
 */
interface ObserverInterface
{
    /**
     * Process current event
     *
     * @param Event $event
     * @return void
     */
    public function process(Event $event);
}

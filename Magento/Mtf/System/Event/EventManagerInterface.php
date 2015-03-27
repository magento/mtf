<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\System\Event;

/**
 * Interface EventManagerInterface
 */
interface EventManagerInterface
{
    /**
     * Dispatches event and call all observers attached to it
     *
     * @param array $eventTags
     * @param array $subjects
     * @return void
     */
    public function dispatchEvent(array $eventTags, array $subjects = []);
}

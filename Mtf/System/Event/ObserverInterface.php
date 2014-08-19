<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Mtf\System\Event;

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

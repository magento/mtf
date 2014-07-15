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
 * @package Mtf\System\Event
 */
interface ObserverInterface
{
    /**
     * @param Event $event
     * @return void
     */
    public function process(Event $event);
}

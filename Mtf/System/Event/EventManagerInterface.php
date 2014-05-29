<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Mtf\System\Event;

interface EventManagerInterface
{
    /**
     * Dispatches event and call all observers attached to it
     *
     * @param array $eventTags
     * @param array $subjects
     * @param string $eventName
     * @return void
     */
    public function dispatchEvent(array $eventTags, array $subjects = [], $eventName = '');
}

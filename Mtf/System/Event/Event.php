<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Mtf\System\Event;

use Mtf\System\Event\State;

/**
 * Class Event
 * @package Mtf\System\Event
 */
class Event
{
    /**
     * Data of the event to be logged - such as objects, locator etc
     *
     * @var string[]
     */
    public $subjects;

    /**
     * Tags for event
     *
     * @var string[]
     */
    public $tags;

    /**
     * Custom event name given by user
     *
     * @var string
     */
    public $eventName;

    /**
     * @param State $state
     * @param array $tags
     * @param array $subjects
     * @param string $eventName
     */
    public function __construct(
        State $state,
        array $tags,
        array $subjects,
        $eventName
    ) {
        $this->tags = $tags;
        $this->subjects = $subjects;
        $this->eventName = $eventName;
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * @return string[]
     */
    public function getSubjects()
    {
        return $this->subjects;
    }

    /**
     * @return string[]
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Returns event identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return microtime(true) . '-' . sha1(
            $this->tags
            . $this->state->getTestSuiteName()
            . $this->state->getTestClassName()
            . $this->state->getTestMethodName()
            . $this->state->getPageUrl()
        );
    }
}

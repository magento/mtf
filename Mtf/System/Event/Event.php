<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Mtf\System\Event;

/**
 * Class containing Event info
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
     * State of the application, containg testcase, testmethod etc of the application run
     *
     * @var State
     */
    public $state;

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
            implode('', $this->tags)
            . $this->state->getTestSuiteName()
            . $this->state->getTestClassName()
            . $this->state->getTestMethodName()
            . $this->state->getPageUrl()
        );
    }

    /**
     * Retrieve message context prefix
     *
     * @return string
     */
    public function getMessagePrefix()
    {
        return sprintf(
            '%s %s %s %s %s %s %s',
            date("Y-m-d H:i:sP"),
            $this->getIdentifier(),
            $this->state->getTestSuiteName(),
            $this->state->getTestClassName(),
            $this->state->getTestMethodName(),
            $this->state->getStageName(),
            $this->state->getPageUrl()
        );
    }
}

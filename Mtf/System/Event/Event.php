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
     * State of the application, containg testcase, testmethod etc of the application run
     *
     * @var State
     */
    public $state;

    /**
     * @param State $state
     * @param array $tags
     * @param array $subjects
     */
    public function __construct(
        State $state,
        $tags,
        array $subjects
    ) {
        $this->tags = $tags;
        $this->subjects = $subjects;
        $this->state = $state;
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
        return sha1(
            microtime(true)
            . implode('', $this->tags)
            . State::getTestSuiteName()
            . State::getTestClassName()
            . State::getTestMethodName()
            . $this->state->getPageUrl()
        );
    }
}

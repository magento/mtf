<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Mtf\System\Event;

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
     * Current called test class
     *
     * @var string
     */
    public $testClassName;

    /**
     * Curent called test method
     *
     * @var string
     */
    public $testMethodName;

    /**
     * Custom event name given by user
     *
     * @var string
     */
    public $eventName;

    /**
     * @param array $tags
     * @param array $subjects
     * @param $eventName
     * @param $testClassName
     * @param $testMethodName
     */
    public function __construct(
        array $tags,
        array $subjects,
        $eventName,
        $testClassName,
        $testMethodName
    ) {
        $this->tags = $tags;
        $this->subjects = $subjects;
        $this->eventName = $eventName;
        $this->testClassName = $testClassName;
        $this->testMethodName = $testMethodName;
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
     * @return string
     */
    public function getTestClassName()
    {
        return $this->testClassName;
    }

    /**
     * @return string
     */
    public function getTestMethodName()
    {
        return $this->testMethodName;
    }
}

<?php
/**
 * Copyright © 2017 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\System\Event;

/**
 * Class containing Event info.
 */
class Event
{
    /**
     * Data of the event to be logged - such as objects, locator etc.
     *
     * @var string[]
     */
    public $subjects;

    /**
     * Tags for event.
     *
     * @var string[]
     */
    public $tags;

    /**
     * State of the application, containing testcase, test method etc of the application run.
     *
     * @var State
     */
    public $state;

    /**
     * Unique event identifier.
     *
     * @var string
     */
    private $identifier;

    /**
     * Unique file event identifier.
     *
     * @var string
     */
    private $fileIdentifier;

    /**
     * @constructor
     * @param State $state
     * @param array $tags
     * @param array $subjects
     */
    public function __construct(
        State $state,
        array $tags,
        array $subjects
    ) {
        $this->tags = $tags;
        $this->subjects = $subjects;
        $this->state = $state;
    }

    /**
     * Get event subjects.
     *
     * @return string[]
     */
    public function getSubjects()
    {
        return $this->subjects;
    }

    /**
     * Get event tags.
     *
     * @return string[]
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Returns event identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        if (!$this->identifier) {
            $this->identifier = sha1(
                microtime(true)
                . implode('', $this->tags)
                . State::getTestSuiteName()
                . State::getTestClassName()
                . State::getTestMethodName()
                . $this->state->getPageUrl()
            );
        }
        return $this->identifier;
    }

    /**
     * Returns event identifier for file names.
     *
     * @return string
     */
    public function getFileIdentifier()
    {
        if (!$this->fileIdentifier) {
            $this->fileIdentifier = date("Y-m-d H-i-s") . substr((string)microtime(), 1, 6)
                . ' (' . substr($this->getIdentifier(), 0, 10) . ')';
        }
        return $this->fileIdentifier;
    }
}

<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Magento\Mtf\System\Event;

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
     * Unique event identifier
     *
     * @var string
     */
    private $identifier;

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
}

<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Constraint;

use Magento\Mtf\ObjectManager;
use Magento\Mtf\System\Event\EventManagerInterface;

/**
 * Constraint objects are stateful,
 * so when need to call for assertions more then one time,
 * the object should be configured again (see method "configure")
 *
 * @api
 * @abstract
 */
abstract class AbstractConstraint extends \PHPUnit_Framework_Constraint implements ConstraintInterface
{
    /**
     * Object Manager.
     *
     * @var \Magento\Mtf\ObjectManager
     */
    protected $objectManager;

    /**
     * Event manager instance.
     *
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * Test Case.
     *
     * @var \PHPUnit_Framework_TestCase
     */
    protected $testCase;

    /**
     * Test Case Name.
     *
     * @var string
     */
    protected $testCaseName = '';

    /**
     * Test Case DI Arguments.
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * Assertion Result.
     *
     * @var boolean
     */
    protected $result;

    /**
     * Severity low | high | middle.
     *
     * @var string
     */
    protected $severity;

    /**
     * Active flag.
     *
     * @var bool
     */
    protected $active;

    /**
     * @constructor
     * @param ObjectManager $objectManager
     * @param EventManagerInterface $eventManager
     * @param string $severity
     * @param bool $active
     */
    public function __construct(
        ObjectManager $objectManager,
        EventManagerInterface $eventManager,
        $severity = 'low',
        $active = true
    ) {
        $this->objectManager = $objectManager;
        $this->eventManager = $eventManager;
        $this->severity = $severity;
        $this->active = $active;
    }

    /**
     * Set DI Arguments to Constraint.
     *
     * @param array $arguments
     * @return void
     */
    public function configure(array $arguments = [])
    {
        $this->result = null;
        $this->arguments = $arguments;
    }

    /**
     * Get constraint result.
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Get class representation is string format.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Is Constraint enabled.
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Evaluates the constraint for test case.
     *
     * @param string $testCaseName
     * @return bool
     */
    protected function matches($testCaseName)
    {
        if ($this->result === null) {
            $this->eventManager->dispatchEvent(
                ['execution'],
                ['[start constraint execution ' . get_class($this) . ']']
            );
            $this->testCaseName = $testCaseName;
            $this->result = true;
            $this->objectManager->invoke($this, 'processAssert', $this->arguments);
        }
        return $this->result;
    }
}

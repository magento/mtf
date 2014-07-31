<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\TestSuite;

/**
 * Class Callback
 * Simple wrapper over regular Test Suite to provide ability for callbacks prior Test Suite run
 *
 * @package Mtf\TestCase
 * @api
 */
class Callback extends \PHPUnit_Framework_TestSuite
{
    /**
     * @var Callable
     */
    protected $callback;

    /**
     * @var array
     */
    protected $arguments;

    /**
     * @var \Mtf\TestSuite\TestSuiteFactory
     */
    protected $factory;

    /**
     * @param TestSuiteFactory $factory
     * @param array $arguments
     * @param mixed $theClass
     * @param string $name
     */
    public function __construct(
        \Mtf\TestSuite\TestSuiteFactory $factory,
        array $arguments = [],
        $theClass = '',
        $name = ''
    ) {
        $this->factory = $factory;
        $this->arguments = $arguments;
        parent::__construct($theClass, $name);
    }

    /**
     * Run callback
     *
     * @param \PHPUnit_Framework_TestResult $result
     * @return \PHPUnit_Framework_TestResult | void
     */
    public function run(\PHPUnit_Framework_TestResult $result = null)
    {
        $testClass = $this->factory->create($this->getName(), $this->arguments);
        return $testClass->run($result);
    }

    /**
     * Avoid attempt to serialize callback
     *
     * @return array
     */
    public function __sleep()
    {
        return ['arguments'];
    }
}

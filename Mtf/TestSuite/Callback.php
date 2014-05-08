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
     * Set callback
     *
     * @param Callable $callback
     * @param array $arguments
     * @return void
     */
    public function setCallback($callback, array $arguments = [])
    {
        $this->callback = $callback;
        $this->arguments = $arguments;
    }

    /**
     * Run callback
     *
     * @param \PHPUnit_Framework_TestResult $result
     * @return \PHPUnit_Framework_TestResult | void
     */
    public function run(\PHPUnit_Framework_TestResult $result = null)
    {
        if ($this->callback) {
            $function = $this->callback;
            return $function($result);
        }
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\TestSuite;

/**
 * Class CallbackTestCase
 * Simple wrapper over regular Test Suite to provide ability for callbacks prior Test Suite run
 *
 * @package Mtf\TestCase
 * @api
 */
class Callback extends \PHPUnit_Framework_TestSuite
{
    /**
     * @var array
     */
    protected $callback;

    /**
     * @var array
     */
    protected $arguments;

    /**
     * Set callback
     *
     * @param array $callback
     * @param array $arguments
     */
    public function setCallback(array $callback, array $arguments = [])
    {
        $this->callback = $callback;
        $this->arguments = $arguments;
    }

    /**
     * Run callback
     *
     * @param \PHPUnit_Framework_TestResult $result
     * @return \PHPUnit_Framework_TestResult
     */
    public function run(\PHPUnit_Framework_TestResult $result = null)
    {
        if ($this->callback) {
            call_user_func_array($this->callback, $this->arguments);
        }

        return parent::run($result);
    }
}

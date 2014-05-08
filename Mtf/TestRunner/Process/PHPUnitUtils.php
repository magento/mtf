<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Mtf\TestRunner\Process;

/**
 * Class Process
 *
 */
class PHPUnitUtils extends \PHPUnit_Util_PHP
{
    /**
     * Returns the location of the PHP binary. Makes the inherited method public.
     *
     * @return string
     */
    public function getPhpBinary()
    {
        return parent::getPhpBinary();
    }

    /**
     * Inherited method from \PHPUnit_Util_PHP but should not be called.
     *
     * @param string $job
     * @param \PHPUnit_Framework_Test $test
     * @param \PHPUnit_Framework_TestResult $result
     * @return array|null|void
     * @throws \PHPUnit_Framework_Exception
     */
    public function runJob($job, \PHPUnit_Framework_Test $test = null, \PHPUnit_Framework_TestResult $result = null)
    {
        throw new \PHPUnit_Framework_Exception(
            'Should not call this method.'
        );
    }

    /**
     * Override the protected method to not do anything when processing a pipe.
     *
     * @param resource $pipe
     * @param string $job
     * @return void
     */
    protected function process($pipe, $job)
    {
        /* do nothing */
    }

    /**
     * Makes the processChildResult inherited method public.
     *
     * @param \PHPUnit_Framework_Test $test
     * @param \PHPUnit_Framework_TestResult $result
     * @param string $stdout
     * @param string $stderr
     * @return void
     */
    public function processChildResult(
        \PHPUnit_Framework_Test $test,
        \PHPUnit_Framework_TestResult $result,
        $stdout,
        $stderr
    ) {
        parent::processChildResult($test, $result, $stdout, $stderr);
    }
}

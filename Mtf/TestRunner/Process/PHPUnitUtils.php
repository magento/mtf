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
    public function getPhpBinary()
    {
        return parent::getPhpBinary();
    }

    public function runJob($job, \PHPUnit_Framework_Test $test = NULL, \PHPUnit_Framework_TestResult $result = NULL)
    {
        throw new PHPUnit_Framework_Exception(
          'Should not call this method.'
        );
    }

    protected function process($pipe, $job)
    {
        /* do nothing */
    }

    public function processChildResult(\PHPUnit_Framework_Test $test, \PHPUnit_Framework_TestResult $result, $stdout, $stderr)
    {
        return parent::processChildResult($test, $result, $stdout, $stderr);
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Mtf\TestRunner\Process;

class TestResultException extends \PHPUnit_Framework_AssertionFailedError
{
    /**
     * @param \Exception $exception
     */
    public function __construct(\Exception $exception)
    {
        parent::__construct($exception);
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        return ['message'];
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Mtf\TestRunner\Process;

class TestResultException extends \Exception
{
    /**
     * @param \Exception $e
     */
    public function __construct(\Exception $e)
    {
        parent::__construct($e->getMessage(), $e->getCode());
        $this->file = $e->getFile();
        $this->line = $e->getLine();
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        return ['message', 'line', 'code', 'file'];
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Exception;

use Mtf\System\Event\EventManager;

/**
 * MTF Class Exception
 *
 * @package Mtf\Exception
 */
class Exception extends ExceptionInterface
{
    /**
     * @var EventManager
     */
    protected $eventManager;

    /**
     * @param string $message
     * @param int $code
     * @param Exception $previous
     * @param EventManager $eventManager
     */
    public function __construct($message = "", $code = 0, Exception $previous = null, EventManager $eventManager)
    {
        $this->eventManager = $eventManager;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Return Exception Message
     * in Human Readable Format
     *
     * @return string
     */
    public function __toString()
    {
        $message = $this->getMessage() . ': ' . $this->getCode() . PHP_EOL
            . $this->getPrevious()->getMessage()
            . PHP_EOL . $this->getPrevious()->getTraceAsString();
        return $message;
    }
}

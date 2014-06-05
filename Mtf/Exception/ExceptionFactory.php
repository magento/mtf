<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Exception;

use Mtf\System\Event\EventManager;
use Mtf\ObjectManager;

/**
 * Class ExceptionFactory
 *
 * @package Mtf\Exception
 */
class ExceptionFactory
{
    /**
     * @var \Mtf\ObjectManager
     */
    protected $objectManager;

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param string $message
     * @param string|int $code
     * @param string $prev
     * @param EventManager $eventManager
     * @return \Mtf\Exception\Exception
     */
    public function create($message, $code, $prev, EventManager $eventManager)
    {
        return $this->objectManager->create(
            'Mtf\Exception\ExceptionInterface',
            [
                'message' => $message,
                'code' => $code,
                'prev' => $prev,
                'eventManager' => $eventManager
            ]
        );
    }

} 

<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Mtf\System\Event;

use Mtf\ObjectManager;

/**
 * Class EventFactory
 * @package Mtf\System\Event
 */
class EventFactory
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
     * @param array $tags
     * @param array $subjects
     * @param string $eventName
     * @param string $testClassName
     * @param string $testMethodName
     * @return \Mtf\System\Event\Event
     */
    public function create(array $tags, array $subjects, $eventName, $testClassName, $testMethodName)
    {
        return $this->objectManager->create('Mtf\System\Event\Event',
            [
                'tags' => $tags,
                'subjects' => $subjects,
                'eventName' => $eventName,
                'testClassName' => $testClassName,
                'testMethodName' => $testMethodName
            ]
        );
    }
}

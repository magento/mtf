<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Mtf\System\Event;

use Mtf\System\Event\Config;

/**
 * Class EventManager
 */
class EventManager implements EventManagerInterface
{
    /**
     * Test class name
     *
     * @var string
     */
    protected $testClass;

    /**
     * Test method name
     *
     * @var string
     */
    protected $testMethod;

    /**
     * Event Factory class object
     *
     * @var EventFactory
     */
    protected $eventFactory;

    /**
     * Class for keeping observers pool
     *
     * @var ObserverPool
     */
    protected $observerPool;

    /**
     * Map of observers and event tags for current preset defined in ENV
     *
     * @var array
     */
    protected $map;

    /**
     * Constructor
     *
     * @param EventFactory $eventFactory
     * @param ObserverPool $observerPool
     * @param Config $config
     */
    public function __construct(
        EventFactory $eventFactory,
        ObserverPool $observerPool,
        Config $config
    ) {
        $this->observerPool = $observerPool;
        $this->eventFactory = $eventFactory;
        $this->map = $config->getObservers();
    }

    /**
     * Dispatches event and call all observers attached to it
     *
     * @param array $eventTags
     * @param array $subjects
     * @return void
     */
    public function dispatchEvent(array $eventTags, array $subjects = [])
    {
        $event = $this->eventFactory->create(
            $eventTags,
            $subjects
        );
        foreach ($this->map as $observerName => $observerTags) {
            if (array_intersect($observerTags, $event->getTags())) {
                $observer = $this->observerPool->getObserver($observerName);
                $observer->process($event);
            }
        }
    }
}

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
 * @package Mtf\System\Event
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
     * Config class object
     *
     * @var \Mtf\System\Event\Config
     */
    protected $config;

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
        $this->config = $config;
    }

    /**
     * Dispatches event and call all observers attached to it
     *
     * @param array $eventTags
     * @param array $subjects
     * @param string $eventName
     * @return void
     */
    public function dispatchEvent(array $eventTags, array $subjects = [], $eventName = '')
    {
        $event = $this->eventFactory->create(
            $eventTags,
            $subjects,
            $eventName
        );
        $map = $this->config->getObservers();
        foreach ($map as $observerName => $observerTags) {
            if (array_intersect($observerTags, $event->getTags())) {
                $observer = $this->observerPool->getObserver($observerName);
                $observer->process($event);
            }
        }
    }
}

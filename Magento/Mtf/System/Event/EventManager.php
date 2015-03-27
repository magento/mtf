<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\System\Event;

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
     * @param \Magento\Mtf\Config\DataInterface $configuration
     */
    public function __construct(
        EventFactory $eventFactory,
        ObserverPool $observerPool,
        \Magento\Mtf\Config\DataInterface $configuration
    ) {
        $this->observerPool = $observerPool;
        $this->eventFactory = $eventFactory;

        $presetName = isset($_ENV['events_preset'])
            ? $_ENV['events_preset']
            : 'default';

        $this->map = $configuration->get('preset/' . $presetName . '/observer');
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
        $event = $this->eventFactory->create($eventTags, $subjects);
        foreach ($this->map as $observerName => $observerTags) {
            if (array_intersect(array_keys($observerTags['tag']), $event->getTags())) {
                $observer = $this->observerPool->getObserver($observerName);
                $observer->process($event);
            }
        }
    }
}

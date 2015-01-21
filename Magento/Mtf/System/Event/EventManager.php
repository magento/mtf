<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Magento\Mtf\System\Event;

use Magento\Mtf\System\Event\Config;

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

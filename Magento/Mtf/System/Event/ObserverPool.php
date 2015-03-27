<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\System\Event;

use Magento\Mtf\ObjectManager;

/**
 * Class ObserverPool
 */
class ObserverPool
{
    /**
     * @var \Magento\Mtf\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Mtf\System\Event\ObserverInterface[]
     */
    protected $observerPool;

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Returns instance of observer
     *
     * @param string $class
     * @return ObserverInterface
     * @throws \InvalidArgumentException
     */
    public function getObserver($class)
    {
        if (empty($this->observerPool[$class])) {
            $instance = $this->objectManager->create($class);
            if (!$instance instanceof ObserverInterface) {
                throw new \InvalidArgumentException(
                    sprintf('Observer class %s should implement ObserverInterface.', $class)
                );
            }
            $this->observerPool[$class] = $instance;
        }
        return $this->observerPool[$class];
    }
}

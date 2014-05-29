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
 * Class ObserverPool
 * @package Mtf\System\Event
 */
class ObserverPool
{
    /**
     * @var \Mtf\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mtf\System\Event\ObserverInterface[]
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

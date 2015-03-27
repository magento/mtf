<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\App\State;

use Magento\Mtf\ObjectManager;

/**
 * Factory for States
 *
 * @api
 */
class StateFactory
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @constructor
     * @param ObjectManager $objectManager
     */
    public function __construct(
        ObjectManager $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * @param string $class
     * @param array $arguments
     * @return StateInterface
     * @throws \UnexpectedValueException
     */
    public function create($class, array $arguments = [])
    {
        $object = $this->objectManager->create($class, $arguments);
        if (!$object instanceof StateInterface) {
            throw new \UnexpectedValueException(
                sprintf('Block class "%s" has to implement \Magento\Mtf\App\State\StateInterface interface.', $class)
            );
        }

        return $object;
    }
}

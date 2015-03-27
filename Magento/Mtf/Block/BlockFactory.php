<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Block;

use Magento\Mtf\ObjectManager;

/**
 * Factory for Blocks
 *
 * @api
 */
class BlockFactory
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
     * @return BlockInterface
     * @throws \UnexpectedValueException
     */
    public function create($class, array $arguments = [])
    {
        $object = $this->objectManager->create($class, $arguments);
        if (!$object instanceof BlockInterface) {
            throw new \UnexpectedValueException(
                sprintf('Block class "%s" has to implement \Magento\Mtf\Block\BlockInterface interface.', $class)
            );
        }

        return $object;
    }
}

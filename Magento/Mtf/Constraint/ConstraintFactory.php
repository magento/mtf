<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Constraint;

use Magento\Mtf\ObjectManager;

/**
 * Factory for Constraints.
 *
 * There is a convention around class names of Constraint classes
 * In general case constraints should be located by constraint code
 * (see method "getByCode")
 *
 * @api
 */
class ConstraintFactory
{
    /**
     * Object Manager.
     *
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @constructor
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Get Constraint object.
     *
     * @param string $class
     * @return ConstraintInterface
     * @throws \UnexpectedValueException
     */
    public function get($class)
    {
        $object = $this->objectManager->get($class);
        if (!$object instanceof ConstraintInterface) {
            throw new \UnexpectedValueException(
                sprintf('Constraint class "%s" has to implement \Magento\Mtf\Constraint\ConstraintInterface interface.', $class)
            );
        }

        return $object;
    }
}

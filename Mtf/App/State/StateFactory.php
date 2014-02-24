<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\App\State;

use Mtf\ObjectManager;

/**
 * Factory for States
 *
 * @package Mtf\Block
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
            throw new \UnexpectedValueException("Block class '$class' has to implement"
                . '\\Mtf\\App\\State\\StateInterface interface.');
        }

        return $object;
    }
}

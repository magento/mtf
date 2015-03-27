<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Factory;

use Magento\Mtf\ObjectManager;

/**
 * Class AbstractFactory
 */
abstract class AbstractFactory
{
    /**
     * Object Manager instance
     *
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Generated factory entity name
     *
     * @var string
     */
    protected $factoryName = '';

    /**
     * @constructor
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create class via ObjectManager
     *
     * @param string $class
     * @param array $arguments
     * @return mixed
     */
    public function create($class, array $arguments = [])
    {
        $object = $this->objectManager->create($class, $arguments);
        $this->checkInterface($object, $class);
        return $object;
    }


    /**
     * Get class
     *
     * @param string $class
     * @return mixed
     */
    public function get($class)
    {
        $object = $this->objectManager->get($class);
        $this->checkInterface($object, $class);
        return $object;
    }

    /**
     * @param mixed $object
     * @param string $class
     * @return void
     * @throws \UnexpectedValueException
     */
    protected function checkInterface($object, $class)
    {
        $interfaceName = '\\Magento\Mtf\\' . $this->factoryName . '\\' . $this->factoryName . 'Interface';
        if (!$object instanceof $interfaceName) {
            throw new \UnexpectedValueException(
                sprintf(
                    '%s class "%s" has to implement "%s" interface.',
                    $this->factoryName,
                    $class,
                    $interfaceName
                )
            );
        }
    }
}

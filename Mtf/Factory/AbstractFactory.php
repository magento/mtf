<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Factory;

use Mtf\Fixture\FixtureInterface;
use Mtf\ObjectManager;

/**
 * Class AbstractFactory
 *
 * @package Mtf\Factory
 */
abstract class AbstractFactory
{
    /**
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
     * @throws \UnexpectedValueException
     */
    public function create($class, array $arguments = [])
    {
        $object = $this->objectManager->create($class, $arguments);
        $interfaceName = '\\Mtf\\' . $this->factoryName . '\\' . $this->factoryName . 'Interface';
        if (!$object instanceof $interfaceName) {
            throw new \UnexpectedValueException("{$this->factoryName} class '$class' has to implement "
                . "{$interfaceName} interface.");
        }
        return $object;
    }


    /**
     * Get class
     *
     * @param string $class
     * @return mixed
     * @throws \UnexpectedValueException
     */
    public function get($class)
    {
        $object = $this->objectManager->get($class);
        $interfaceName = '\\Mtf\\' . $this->factoryName . '\\' . $this->factoryName . 'Interface';
        if (!$object instanceof $interfaceName) {
            throw new \UnexpectedValueException("{$this->factoryName} class '$class' has to implement "
                . "$interfaceName interface.");
        }
        return $object;
    }
}

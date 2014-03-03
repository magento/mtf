<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf;

/**
 * Class ObjectManager
 *
 * Responsible for instantiating objects taking into account:
 * - constructor arguments (using configured, and provided parameters)
 * - class instances life style (singleton, transient)
 * - interface preferences
 *
 * @package Mtf
 * @api
 */
class ObjectManager extends \Magento\ObjectManager\ObjectManager
{
    /**
     * @var \Mtf\ObjectManager\Factory
     */
    protected $_factory;

    /**
     * @var ObjectManager
     */
    protected static $_instance;

    /**
     * @constructor
     * @param \Mtf\ObjectManager\Factory $factory
     * @param \Magento\ObjectManager\Config $config
     * @param array $sharedInstances
     */
    public function __construct(
        \Mtf\ObjectManager\Factory $factory = null,
        \Magento\ObjectManager\Config $config = null,
        array $sharedInstances = []
    ) {
        parent::__construct($factory, $config, $sharedInstances);
        $this->_sharedInstances['Mtf\ObjectManager'] = $this;
    }

    /**
     * Get list of parameters for class method
     *
     * @param string $type
     * @param string $method
     * @return array|null
     */
    public function getParameters($type, $method)
    {
        return $this->_factory->getParameters($type, $method);
    }

    /**
     * Resolve and prepare arguments for class method
     *
     * @param object $object
     * @param string $method
     * @param array $arguments
     * @return array
     */
    public function prepareArguments($object, $method, array $arguments = [])
    {
        return $this->_factory->prepareArguments($object, $method, $arguments);
    }

    /**
     * Invoke class method with prepared arguments
     *
     * @param object $object
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function invoke($object, $method, array $arguments = [])
    {
        return $this->_factory->invoke($object, $method, $arguments);
    }

    /**
     * Set object manager instance
     *
     * @param ObjectManager $objectManager
     */
    public static function setInstance(ObjectManager $objectManager)
    {
        self::$_instance = $objectManager;
    }

    /**
     * Retrieve object manager
     *
     * @return ObjectManager
     * @throws \RuntimeException
     */
    public static function getInstance()
    {
        if (!self::$_instance instanceof ObjectManager) {
            return false;
        }
        return self::$_instance;
    }
}

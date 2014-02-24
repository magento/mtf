<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\ObjectManager;

use Mtf\System\Code\ClassReader;

/**
 * Class Factory
 *
 * @package Mtf\ObjectManager
 * @internal
 */
class Factory extends \Magento\ObjectManager\Factory\Factory
{
    /**
     * @var \Mtf\System\Code\ClassReader
     */
    protected $_classReader;

    /**
     * @constructor
     * @param \Magento\ObjectManager\Config $config
     * @param \Magento\ObjectManager\ObjectManager $objectManager
     * @param \Magento\ObjectManager\Definition $definitions
     * @param array $globalArguments
     */
    public function __construct(
        \Magento\ObjectManager\Config $config,
        \Magento\ObjectManager\ObjectManager $objectManager = null,
        \Magento\ObjectManager\Definition $definitions = null,
        $globalArguments = []
    ) {
        parent::__construct($config, $objectManager, $definitions, $globalArguments);
        $this->_classReader = new ClassReader();
    }

    /**
     * Invoke class method and prepared arguments
     *
     * @param $object
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function invoke($object, $method, array $args = [])
    {
        $args = $this->prepareArguments($object, $method, $args);

        $type = get_class($object);
        $class = new \ReflectionClass($type);
        $method = $class->getMethod($method);
        return $method->invokeArgs($object, $args);

        /* Comment switch in order to use unordered argument
        switch (count($args)) {
            case 1:
                return $object->$method($args[0]);
            case 2:
                return $object->$method($args[0], $args[1]);
            case 3:
                return $object->$method($args[0], $args[1], $args[2]);
            case 4:
                return $object->$method($args[0], $args[1], $args[2], $args[3]);
            case 5:
                return $object->$method($args[0], $args[1], $args[2], $args[3], $args[4]);
            case 6:
                return $object->$method($args[0], $args[1], $args[2], $args[3], $args[4], $args[5]);
            case 7:
                return $object->$method($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6]);
            case 8:
                return $object->$method($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7]);
            default:
                $type = get_class($object);
                $class = new \ReflectionClass($type);
                $method = $class->getMethod($method);
                return $method->invokeArgs($object, $args);
        } */
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
        return $this->_classReader->getParameters($type, $method);
    }

    /**
     * Resolve and prepare arguments for class method
     *
     * @param string $object
     * @param string $method
     * @param array $arguments
     * @return array
     */
    public function prepareArguments($object, $method, array $arguments = [])
    {
        $type = get_class($object);
        $parameters = $this->_classReader->getParameters($type, $method);
        if ($parameters == null) {
            return [];
        }

        return $this->_resolveArguments($type, $parameters, $arguments);
    }

    /**
     * Overwritten to have parameters passed to nested instances.
     *
     * @param string $requestedType
     * @param array $parameters
     * @param array $arguments
     * @return array
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @throws \BadMethodCallException
     */
    protected function _resolveArguments($requestedType, array $parameters, array $arguments = [])
    {
        $resolvedArguments = [];
        $arguments = $this->_config->getArguments($requestedType, $arguments);

        foreach ($parameters as $parameter) {
            list($paramName, $paramType, $paramRequired, $paramDefault) = $parameter;
            $argument = null;
            if (array_key_exists($paramName, $arguments)) {
                $argument = $arguments[$paramName];
                if ($paramType && is_array($argument) && !isset($argument['instance'])) {
                    $argument['instance'] = $paramType;
                }
            } elseif (array_key_exists('options', $arguments) && array_key_exists($paramName, $arguments['options'])) {
                // The parameter name doesn't exist in the arguments, but it is contained in the 'options' argument.
                $argument = $arguments['options'][$paramName];
            } elseif ($paramRequired) {
                if ($paramType) {
                    $argument = ['instance' => $paramType];
                } else {
                    $this->_creationStack = [];
                    throw new \BadMethodCallException(
                        'Missing required argument $' . $paramName . ' for ' . $requestedType . '.'
                    );
                }
            } else {
                $argument = $paramDefault;
            }
            if ($paramType && !is_object($argument) && $argument !== $paramDefault) {
                if (!is_array($argument) || !isset($argument['instance'])) {
                    $this->_creationStack = [];
                    throw new \InvalidArgumentException(
                        'Invalid parameter configuration provided for $' . $paramName . ' argument in ' . $requestedType
                    );
                }
                $argumentType = $argument['instance'];
                if (isset($this->_creationStack[$argumentType])) {
                    $this->_creationStack = [];
                    throw new \LogicException(
                        'Circular dependency: ' . $argumentType . ' depends on ' . $requestedType . ' and viceversa.'
                    );
                }
                $this->_creationStack[$requestedType] = 1;
                $isShared = (!isset($argument['shared']) && $this->_config->isShared($argumentType))
                    || (isset($argument['shared']) && $argument['shared']);

                if (array_key_exists('instance', $argument)) {
                    unset($argument['instance']);
                }
                if (array_key_exists('shared', $argument)) {
                    unset($argument['shared']);
                }
                $_arguments = !empty($argument) ? $argument : [];

                $argument = $isShared
                    ? $this->_objectManager->get($argumentType)
                    : $this->_objectManager->create($argumentType, $_arguments);
                unset($this->_creationStack[$requestedType]);
            } elseif (is_array($argument) && isset($argument['argument'])) {
                $argKey = $argument['argument'];
                $argument = isset($this->_globalArguments[$argKey]) ? $this->_globalArguments[$argKey] : $paramDefault;
            } else {
                $argument = !empty($argument) ? $argument : $paramDefault;
            }
            $resolvedArguments[$paramName] = $argument;
        }
        return $resolvedArguments;
    }
}

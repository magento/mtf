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
    protected $classReader;

    /**
     * @constructor
     *
     * @param \Magento\ObjectManager\Config $config
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\ObjectManager\Definition $definitions
     * @param array $globalArguments
     */
    public function __construct(
        \Magento\ObjectManager\Config $config,
        \Magento\ObjectManager $objectManager = null,
        \Magento\ObjectManager\Definition $definitions = null,
        $globalArguments = array()
    ) {
        parent::__construct($config, $objectManager, $definitions, $globalArguments);
        $this->classReader = new ClassReader();
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
        return $this->classReader->getParameters($type, $method);
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
        $type = get_class($object);
        $parameters = $this->classReader->getParameters($type, $method);
        if ($parameters == null) {
            return [];
        }

        return $this->_resolveArguments($type, $parameters, $arguments);
    }

    /**
     * Resolve constructor arguments
     *
     * @param string $requestedType
     * @param array $parameters
     * @param array $arguments
     * @return array
     * @throws \UnexpectedValueException
     * @throws \BadMethodCallException
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _resolveArguments($requestedType, array $parameters, array $arguments = array())
    {
        $resolvedArguments = array();
        $arguments = count($arguments)
            ? array_replace($this->config->getArguments($requestedType), $arguments)
            : $this->config->getArguments($requestedType);
        foreach ($parameters as $parameter) {
            list($paramName, $paramType, $paramRequired, $paramDefault) = $parameter;
            $argument = null;
            if (array_key_exists($paramName, $arguments)) {
                $argument = $arguments[$paramName];
            } elseif (array_key_exists('options', $arguments) && array_key_exists($paramName, $arguments['options'])) {
                // The parameter name doesn't exist in the arguments, but it is contained in the 'options' argument.
                $argument = $arguments['options'][$paramName];
            } else if ($paramRequired) {
                if ($paramType) {
                    $argument = array('instance' => $paramType);
                } else {
                    $this->creationStack = array();
                    throw new \BadMethodCallException(
                        'Missing required argument $' . $paramName . ' of ' . $requestedType . '.'
                    );
                }
            } else {
                $argument = $paramDefault;
            }
            if ($paramType && !is_object($argument) && $argument !== $paramDefault) {
                if (!is_array($argument) || !isset($argument['instance'])) {
                    throw new \UnexpectedValueException(
                        'Invalid parameter configuration provided for $' . $paramName . ' argument of ' . $requestedType
                    );
                }
                $argumentType = $argument['instance'];
                $isShared = (isset($argument['shared']) ? $argument['shared'] :$this->config->isShared($argumentType));

                if (array_key_exists('instance', $argument)) {
                    unset($argument['instance']);
                }
                if (array_key_exists('shared', $argument)) {
                    unset($argument['shared']);
                }

                $_arguments = !empty($argument) ? $argument : [];

                $argument = $isShared
                    ? $this->objectManager->get($argumentType)
                    : $this->objectManager->create($argumentType, $_arguments);
            } else if (is_array($argument)) {
                if (isset($argument['argument'])) {
                    $argKey = $argument['argument'];
                    $argument = isset($this->globalArguments[$argKey]) ? $this->globalArguments[$argKey] : $paramDefault;
                } else {
                    $this->parseArray($argument);
                }
            }
            $resolvedArguments[] = $argument;
        }
        return $resolvedArguments;
    }

    /**
     * Parse array argument
     *
     * @param $array
     * @return void
     */
    protected function parseArray(&$array)
    {
        foreach ($array as $key => $item) {
            if (is_array($item)) {
                if (isset($item['instance'])) {
                    $itemType = $item['instance'];
                    $isShared = (isset($item['shared'])) ? $item['shared'] : $this->config->isShared($itemType);

                    unset($item['instance']);
                    if (array_key_exists('shared', $item)) {
                        unset($item['shared']);
                    }

                    $_arguments = !empty($item) ? $item : [];

                    $array[$key] = $isShared
                        ? $this->objectManager->get($itemType)
                        : $this->objectManager->create($itemType, $_arguments);
                } elseif (isset($item['argument'])) {
                    $array[$key] = isset($this->globalArguments[$item['argument']])
                        ? $this->globalArguments[$item['argument']]
                        : null;
                } else {
                    $this->parseArray($item);
                }
            }
        }
    }
}

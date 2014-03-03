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
     * @param \Magento\ObjectManager\Config $config
     * @param \Magento\Data\Argument\InterpreterInterface $argInterpreter
     * @param \Magento\ObjectManager\Config\Argument\ObjectFactory $argObjectFactory
     * @param \Magento\ObjectManager\Definition $definitions
     */
    public function __construct(
        \Magento\ObjectManager\Config $config,
        \Magento\Data\Argument\InterpreterInterface $argInterpreter,
        \Magento\ObjectManager\Config\Argument\ObjectFactory $argObjectFactory,
        \Magento\ObjectManager\Definition $definitions = null
    ) {
        parent::__construct($config, $argInterpreter, $argObjectFactory, $definitions);
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
     * Resolve constructor arguments.
     * Overwritten to have parameters passed via names
     *
     * @param string $requestedType
     * @param array $parameters
     * @param array $argumentValues
     * @return array
     * @throws \UnexpectedValueException
     * @throws \BadMethodCallException
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _resolveArguments($requestedType, array $parameters, array $argumentValues = array())
    {
        $result = array();
        $arguments = $this->_config->getArguments($requestedType);
        foreach ($parameters as $parameter) {
            list($paramName, $paramType, $paramRequired, $paramDefault) = $parameter;
            if (array_key_exists($paramName, $argumentValues)) {
                $value = $argumentValues[$paramName];
                if ($paramType && is_array($value)) {
                    $value = $this->_argObjectFactory->getObjectManager()->create($paramType, $value);
                }
            } else if (array_key_exists($paramName, $arguments)) {
                $argumentData = $arguments[$paramName];
                if (!is_array($argumentData)) {
                    throw new \UnexpectedValueException(sprintf(
                        'Invalid parameter configuration provided for $%s argument of %s.', $paramName, $requestedType
                    ));
                }
                try {
                    $value = $this->_argInterpreter->evaluate($argumentData);
                } catch (\Magento\Data\Argument\MissingOptionalValueException $e) {
                    $value = $paramDefault;
                }
            } else if ($paramRequired) {
                if (!$paramType) {
                    throw new \BadMethodCallException(sprintf(
                        'Missing required argument $%s of %s.', $paramName, $requestedType
                    ));
                }
                $value = $this->_argObjectFactory->create($paramType);
            } else {
                $value = $paramDefault;
            }
            $result[$paramName] = $value;
        }
        return $result;
    }
}

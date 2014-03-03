<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\ObjectManager\Factory;

class Factory implements \Magento\ObjectManager\Factory
{
    /**
     * @var \Magento\ObjectManager\Config
     */
    protected $_config;

    /**
     * Definition list
     *
     * @var \Magento\ObjectManager\Definition
     */
    protected $_definitions;

    /**
     * @var array
     */
    protected $_creationStack = array();

    /**
     * @var \Magento\Data\Argument\InterpreterInterface
     */
    protected $_argInterpreter;

    /**
     * @var \Mtf\ObjectManager\Config\Argument\ObjectFactory
     */
    protected $_argObjectFactory;

    /**
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
        $this->_config = $config;
        $this->_argInterpreter = $argInterpreter;
        $this->_argObjectFactory = $argObjectFactory;
        $this->_definitions = $definitions ? : new \Magento\ObjectManager\Definition\Runtime();
    }

    /**
 * Resolve constructor arguments
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
            $result[] = $value;
        }
        return $result;
    }

    /**
     * Create instance with call time arguments
     *
     * @param string $requestedType
     * @param array $arguments
     * @return object
     * @throws \Exception
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function create($requestedType, array $arguments = array())
    {
        $type = $this->_config->getInstanceType($requestedType);
        $parameters = $this->_definitions->getParameters($type);
        if ($parameters == null) {
            return new $type();
        }
        $this->_assertNoCircularDependency($requestedType);
        $this->_creationStack[$requestedType] = $requestedType;
        try {
            $args = $this->_resolveArguments($requestedType, $parameters, $arguments);
            unset($this->_creationStack[$requestedType]);
        } catch (\Exception $e) {
            unset($this->_creationStack[$requestedType]);
            throw $e;
        }

        $reflection = new \ReflectionClass($type);
        return $reflection->newInstanceArgs($args);
    }

    /**
     * Prevent circular dependencies using creation stack
     *
     * @param string $type
     * @throws \LogicException
     */
    private function _assertNoCircularDependency($type)
    {
        if (isset($this->_creationStack[$type])) {
            $lastFound = end($this->_creationStack);
            $this->_creationStack = array();
            throw new \LogicException("Circular dependency: {$type} depends on {$lastFound} and vice versa.");
        }
    }
}

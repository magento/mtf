<?php
/**
 * Class constructor validator
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Code\Validator;

use Magento\Code\ValidatorInterface;
use Magento\Code\ValidationException;

class ContextAggregation implements ValidatorInterface
{
    /**
     * @var \Magento\Code\Reader\ArgumentsReader
     */
    protected $_argumentsReader;

    /**
     * @param \Magento\Code\Reader\ArgumentsReader $argumentsReader
     */
    public function __construct(\Magento\Code\Reader\ArgumentsReader $argumentsReader = null)
    {
        $this->_argumentsReader = $argumentsReader ?: new \Magento\Code\Reader\ArgumentsReader();
    }

    /**
     * Validate class. Check declaration of dependencies that already declared in context object
     *
     * @param string $className
     * @return bool
     * @throws ValidationException
     */
    public function validate($className)
    {
        $class = new \ReflectionClass($className);
        $classArguments = $this->_argumentsReader->getConstructorArguments($class);

        $errors = array();
        $contextDependencies = array();

        $actualDependencies = $this->_getObjectArguments($classArguments);

        foreach ($actualDependencies as $type) {
            /** Check if argument is context object */
            if (is_subclass_of($type, '\Magento\ObjectManager\ContextInterface')) {
                $contextDependencies = array_merge(
                    $contextDependencies,
                    $this->_argumentsReader->getConstructorArguments(new \ReflectionClass($type), false, true)
                );
            }
        }

        $contextDependencyTypes = $this->_getObjectArguments($contextDependencies);

        foreach ($actualDependencies as $type) {
            if (in_array($type, $contextDependencyTypes)) {
                $errors[] = $type . ' already exists in context object';
            }
        }

        if (false == empty($errors)) {
            $classPath = str_replace('\\', '/', $class->getFileName());
            throw new ValidationException(
                'Incorrect dependency in class ' . $className . ' in ' . $classPath . PHP_EOL
                . implode(PHP_EOL, $errors)
            );
        }
        return true;
    }


    /**
     * Get arguments with object types
     *
     * @param array $arguments
     * @return array
     */
    protected function _getObjectArguments(array $arguments)
    {
        $output = array();
        foreach ($arguments as $argument) {
            $type = $argument['type'];
            if (!$type || $type == 'array') {
                continue;
            }
            $output[] = $type;
        }

        return $output;
    }

}

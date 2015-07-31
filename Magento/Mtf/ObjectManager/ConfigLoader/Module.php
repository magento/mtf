<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\ObjectManager\ConfigLoader;

/**
 * Class Module
 * MTF Modular configuration loader. Loads configuration from XML files, located in modules' directories
 *
 * @internal
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Module
{
    /**
     * Framework mode
     *
     * @var string
     */
    protected $_appMode = 'developer';

    /**
     * Load MTF configuration from .xml files
     *
     * @param null $scope
     * @return array
     */
    public function load($scope = null)
    {
        $reader = new \Magento\Mtf\ObjectManager\Config\Reader\Dom(
            new \Magento\Mtf\Config\FileResolver\Module(),
            new \Magento\Mtf\ObjectManager\Config\Mapper\Dom($this->createArgumentInterpreter()),
            new \Magento\Mtf\ObjectManager\Config\SchemaLocator(),
            new \Magento\Mtf\Config\ValidationState($this->_appMode)
        );

        return $reader->read($scope);
    }

    /**
     * Return newly created instance on an argument interpreter, suitable for processing DI arguments
     *
     * @return \Magento\Mtf\Data\Argument\InterpreterInterface
     */
    protected function createArgumentInterpreter()
    {
        $booleanUtils = new \Magento\Mtf\Stdlib\BooleanUtils();
        $constInterpreter = new \Magento\Mtf\Data\Argument\Interpreter\Constant();
        $result = new \Magento\Mtf\Data\Argument\Interpreter\Composite(
            [
                'boolean' => new \Magento\Mtf\Data\Argument\Interpreter\Boolean($booleanUtils),
                'string' => new \Magento\Mtf\Data\Argument\Interpreter\StringType($booleanUtils),
                'number' => new \Magento\Mtf\Data\Argument\Interpreter\Number(),
                'null' => new \Magento\Mtf\Data\Argument\Interpreter\NullType(),
                'object' => new \Magento\Mtf\Data\Argument\Interpreter\ObjectType($booleanUtils),
                'const' => $constInterpreter,
                'init_parameter' => new \Magento\Mtf\Data\Argument\Interpreter\Argument($constInterpreter)
            ],
            \Magento\Mtf\ObjectManager\Config\Reader\Dom::TYPE_ATTRIBUTE
        );
        // Add interpreters that reference the composite
        $result->addInterpreter('array', new \Magento\Mtf\Data\Argument\Interpreter\ArrayType($result));
        return $result;
    }
}

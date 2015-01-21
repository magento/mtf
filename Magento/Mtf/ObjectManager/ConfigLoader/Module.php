<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
                'string' => new \Magento\Mtf\Data\Argument\Interpreter\String($booleanUtils),
                'number' => new \Magento\Mtf\Data\Argument\Interpreter\Number(),
                'null' => new \Magento\Mtf\Data\Argument\Interpreter\NullType(),
                'object' => new \Magento\Mtf\Data\Argument\Interpreter\Object($booleanUtils),
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

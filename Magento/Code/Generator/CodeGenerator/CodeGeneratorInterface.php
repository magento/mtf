<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Code
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Code\Generator\CodeGenerator;

interface CodeGeneratorInterface extends \Zend\Code\Generator\GeneratorInterface
{
    /**
     * @param string $name
     * @return \Magento\Code\Generator\CodeGenerator\CodeGeneratorInterface
     */
    public function setName($name);

    /**
     * @param array $docBlock
     * @return \Magento\Code\Generator\CodeGenerator\CodeGeneratorInterface
     */
    public function setClassDocBlock(array $docBlock);

    /**
     * @param array $properties
     * @return \Magento\Code\Generator\CodeGenerator\CodeGeneratorInterface
     */
    public function addProperties(array $properties);

    /**
     * @param array $methods
     * @return \Magento\Code\Generator\CodeGenerator\CodeGeneratorInterface
     */
    public function addMethods(array $methods);

    /**
     * @param string $extendedClass
     * @return \Magento\Code\Generator\CodeGenerator\CodeGeneratorInterface
     */
    public function setExtendedClass($extendedClass);

    /**
     * setImplementedInterfaces()
     *
     * @param array $interfaces
     * @return \Magento\Code\Generator\CodeGenerator\CodeGeneratorInterface
     */
    public function setImplementedInterfaces(array $interfaces);
}

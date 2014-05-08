<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Code
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Code\Generator\CodeGenerator;

interface CodeGeneratorInterface extends \Zend\Code\Generator\GeneratorInterface
{
    /**
     * @param string $name
     * @return \Magento\Framework\Code\Generator\CodeGenerator\CodeGeneratorInterface
     */
    public function setName($name);

    /**
     * @param array $docBlock
     * @return \Magento\Framework\Code\Generator\CodeGenerator\CodeGeneratorInterface
     */
    public function setClassDocBlock(array $docBlock);

    /**
     * @param array $properties
     * @return \Magento\Framework\Code\Generator\CodeGenerator\CodeGeneratorInterface
     */
    public function addProperties(array $properties);

    /**
     * @param array $methods
     * @return \Magento\Framework\Code\Generator\CodeGenerator\CodeGeneratorInterface
     */
    public function addMethods(array $methods);

    /**
     * @param string $extendedClass
     * @return \Magento\Framework\Code\Generator\CodeGenerator\CodeGeneratorInterface
     */
    public function setExtendedClass($extendedClass);

    /**
     * setImplementedInterfaces()
     *
     * @param array $interfaces
     * @return \Magento\Framework\Code\Generator\CodeGenerator\CodeGeneratorInterface
     */
    public function setImplementedInterfaces(array $interfaces);
}

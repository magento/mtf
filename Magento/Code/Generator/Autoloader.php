<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Code\Generator;

class Autoloader
{
    /**
     * @var \Magento\Code\Generator
     */
    protected $_generator;

    /**
     * @param \Magento\Code\Generator $generator
     */
    public function __construct(\Magento\Code\Generator $generator)
    {
        $this->_generator = $generator;
    }

    /**
     * Load specified class name and generate it if necessary
     *
     * @param string $className
     * @return void
     */
    public function load($className)
    {
        if (!class_exists($className)) {
            if (\Magento\Code\Generator::GENERATION_SUCCESS === $this->_generator->generateClass($className)) {
                \Magento\Autoload\IncludePath::load($className);
            }
        }
    }
}

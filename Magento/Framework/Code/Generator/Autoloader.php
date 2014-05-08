<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Code\Generator;

use Magento\Framework\Code\Generator;

class Autoloader
{
    /**
     * @var Generator
     */
    protected $_generator;

    /**
     * @param Generator $generator
     */
    public function __construct(Generator $generator)
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
            if (Generator::GENERATION_SUCCESS === $this->_generator->generateClass($className)) {
                \Magento\Framework\Autoload\IncludePath::load($className);
            }
        }
    }
}

<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\Util\Generate;

use Magento\Mtf\ObjectManagerInterface;

/**
 * Class Generate
 *
 * Abstract Generate
 *
 * @api
 * @abstract
 */
abstract class AbstractGenerate
{
    /**
     * Counter
     *
     * @var int
     */
    protected $cnt = 0;

    /**
     * An array of errors
     *
     * @var string[]
     */
    private $errors = [];

    /**
     * @var \Magento\Mtf\ObjectManager
     */
    protected $objectManager;

    /**
     * @constructor
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * Launch generation of all classes
     *
     * @return mixed
     */
    abstract public function launch();

    /**
     * Generate single class
     *
     * @param string $className
     * @return string|bool
     */
    abstract public function generate($className);

    /**
     * Convert class name to camel-case
     *
     * @param string $class
     * @return string
     */
    protected function toCamelCase($class)
    {
        $class = str_replace('_', ' ', $class);
        $class = str_replace('\\', ' ', $class);
        $class = str_replace('/', ' ', $class);

        return str_replace(' ', '', ucwords($class));
    }

    /**
     * Prepare data for phpdoc attribute "copyright"
     *
     * @return string
     */
    private function getCopyright()
    {
        return 'Copyright © 2015 Magento. All rights reserved.';
    }

    /**
     * Prepare data for phpdoc attribute "license"
     *
     * @return string
     */
    private function getLicenseNote()
    {
        return 'See COPYING.txt for license details.';
    }

    /**
     * Get file phpdoc with license and copyright information
     *
     * @return string
     */
    protected function getFilePhpDoc()
    {
        $content = "/**\n";
        $content .= " * " . $this->getCopyright() . "\n";
        $content .= " * " . $this->getLicenseNote() .  "\n";
        $content .= " */\n\n";
        return $content;
    }

    /**
     * Add error message
     *
     * @param string $message
     * @return void
     */
    protected function addError($message)
    {
        $this->errors[] = $message;
    }

    /**
     * Get list of occurred errors
     *
     * @return string[]
     */
    public function getErrors()
    {
        return $this->errors;
    }
}

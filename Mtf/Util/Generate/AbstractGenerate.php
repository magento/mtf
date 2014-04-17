<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Util\Generate;

use Magento\Framework\ObjectManager;

/**
 * Class Generate
 *
 * Abstract Generate
 *
 * @package Mtf\Util
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
     * @var \Mtf\ObjectManager
     */
    protected $objectManager;

    /**
     * @constructor
     * @param ObjectManager $objectManager
     */
    public function __construct(
        ObjectManager $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * Launch generators
     *
     * @return mixed
     */
    abstract public function launch();

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
}

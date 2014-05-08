<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\TestSuite;

use Mtf\ObjectManager;

/**
 * Class InjectableDataProvider
 *
 * @package Mtf\TestSuite
 * @api
 */
class InjectableDataProvider extends InjectableMethod
{
    /**
     * @constructor
     * @param string $class
     * @param string $name
     * @param string $path
     */
    public function __construct($class = '', $name = '', $path = '')
    {
        $this->initObjectManager();
        $this->setName($class);
    }
}

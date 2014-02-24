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
 * Class RegularTestCase
 *
 * @package Mtf\TestSuite
 * @api
 */
class RegularTestCase extends \PHPUnit_Framework_TestSuite
{
    /**
     * To accept $class argument name instead of $theClass
     *
     * @constructor
     * @param string $class
     * @param string $name
     */
    public function __construct($class = '', $name = '')
    {
        parent::__construct($class, $name);
    }
}

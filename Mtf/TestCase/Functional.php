<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\TestCase;

/**
 * Class Functional
 *
 * Class is extended from PHPUnit_Framework_TestCase
 * Used for test cases based on old specification
 * "Injectable" abstract Class should be used instead
 *
 * @package Mtf\TestCase
 * @api
 * @abstract
 */
abstract class Functional extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Mtf\ObjectManager
     */
    protected $objectManager;

    /**
     * Constructs a test case with the given name.
     *
     * @constructor
     * @param  string $name
     * @param  array  $data
     * @param  string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->objectManager = \Mtf\ObjectManagerFactory::getObjectManager();

        $this->_construct();
    }

    /**
     * Protected construct for child test cases
     */
    protected function _construct()
    {
        //
    }
}

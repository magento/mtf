<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\TestCase;

use Mtf\ObjectManager;
use Mtf\TestSuite\InjectableTestCase;
use Mtf\TestSuite\RegularTestCase;

/**
 * Class TestCaseFactory
 *
 * @package Mtf\TestCase
 * @api
 */
class TestCaseFactory
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @constructor
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param string $class
     * @param array $arguments
     * @return InjectableTestCase|RegularTestCase
     */
    public function create($class, array $arguments)
    {
        $refClass = new \ReflectionClass($class);
        if ($refClass->isSubclassOf('Mtf\\TestCase\\Injectable')) {
            $object = $this->objectManager->create('Mtf\\TestSuite\\InjectableTestCase', $arguments);
        } else {
            $object = $this->objectManager->create('Mtf\\TestSuite\\RegularTestCase', $arguments);
        }

        return $object;
    }
}

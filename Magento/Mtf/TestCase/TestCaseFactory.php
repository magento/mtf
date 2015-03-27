<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\TestCase;

use Magento\Mtf\ObjectManager;
use Magento\Mtf\TestSuite\InjectableTestCase;
use Magento\Mtf\TestSuite\RegularTestCase;

/**
 * Class TestCaseFactory
 *
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
        if ($refClass->isSubclassOf('Magento\Mtf\\TestCase\\Injectable')) {
            $object = $this->objectManager->create('Magento\Mtf\\TestSuite\\InjectableTestCase', $arguments);
        } else {
            $object = $this->objectManager->create('Magento\Mtf\\TestSuite\\RegularTestCase', $arguments);
        }

        return $object;
    }
}

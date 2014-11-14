<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mtf\Factory;

use Mtf\ObjectManager;

/**
 * Class AbstractFactory
 */
abstract class AbstractFactory
{
    /**
     * Object Manager instance
     *
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Generated factory entity name
     *
     * @var string
     */
    protected $factoryName = '';

    /**
     * @constructor
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create class via ObjectManager
     *
     * @param string $class
     * @param array $arguments
     * @return mixed
     */
    public function create($class, array $arguments = [])
    {
        $object = $this->objectManager->create($class, $arguments);
        $this->checkInterface($object, $class);
        return $object;
    }


    /**
     * Get class
     *
     * @param string $class
     * @return mixed
     */
    public function get($class)
    {
        $object = $this->objectManager->get($class);
        $this->checkInterface($object, $class);
        return $object;
    }

    /**
     * @param mixed $object
     * @param string $class
     * @return void
     * @throws \UnexpectedValueException
     */
    protected function checkInterface($object, $class)
    {
        $interfaceName = '\\Mtf\\' . $this->factoryName . '\\' . $this->factoryName . 'Interface';
        if (!$object instanceof $interfaceName) {
            throw new \UnexpectedValueException(
                sprintf(
                    '%s class "%s" has to implement "%s" interface.',
                    $this->factoryName,
                    $class,
                    $interfaceName
                )
            );
        }
    }
}

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

namespace Magento\Mtf;

/**
 * Class ObjectManager
 *
 * Responsible for instantiating objects taking into account:
 * - constructor arguments (using configured, and provided parameters)
 * - class instances life style (singleton, transient)
 * - interface preferences
 *
 * @api
 */
class ObjectManager extends \Magento\Mtf\ObjectManager\ObjectManager
{
    /**
     * @var \Magento\Mtf\ObjectManager\Factory
     */
    protected $_factory;

    /**
     * @var ObjectManager
     */
    protected static $_instance;

    /**
     * @constructor
     * @param \Magento\Mtf\ObjectManager\Factory $factory
     * @param \Magento\Mtf\ObjectManager\ConfigInterface $config
     * @param array $sharedInstances
     */
    public function __construct(
        \Magento\Mtf\ObjectManager\Factory $factory = null,
        \Magento\Mtf\ObjectManager\ConfigInterface $config = null,
        array $sharedInstances = []
    ) {
        parent::__construct($factory, $config, $sharedInstances);
        $this->_sharedInstances['Magento\Mtf\ObjectManager'] = $this;
    }

    /**
     * Get list of parameters for class method
     *
     * @param string $type
     * @param string $method
     * @return array|null
     */
    public function getParameters($type, $method)
    {
        return $this->_factory->getParameters($type, $method);
    }

    /**
     * Resolve and prepare arguments for class method
     *
     * @param object $object
     * @param string $method
     * @param array $arguments
     * @return array
     */
    public function prepareArguments($object, $method, array $arguments = [])
    {
        return $this->_factory->prepareArguments($object, $method, $arguments);
    }

    /**
     * Invoke class method with prepared arguments
     *
     * @param object $object
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function invoke($object, $method, array $arguments = [])
    {
        return $this->_factory->invoke($object, $method, $arguments);
    }

    /**
     * Set object manager instance
     *
     * @param ObjectManager $objectManager
     * @return void
     */
    public static function setInstance(ObjectManager $objectManager)
    {
        self::$_instance = $objectManager;
    }

    /**
     * Retrieve object manager
     *
     * @return ObjectManager
     * @throws \RuntimeException
     */
    public static function getInstance()
    {
        if (!self::$_instance instanceof ObjectManager) {
            return false;
        }
        return self::$_instance;
    }

    /**
     * Avoid to serialize Closure properties
     *
     * @return array
     */
    public function __sleep()
    {
        return [];
    }
}

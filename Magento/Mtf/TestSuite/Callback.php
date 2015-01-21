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

namespace Magento\Mtf\TestSuite;

/**
 * Class Callback
 * Simple wrapper over regular Test Suite to provide ability for callbacks prior Test Suite run
 *
 * @api
 */
class Callback extends \PHPUnit_Framework_TestSuite
{
    /**
     * @var Callable
     */
    protected $callback;

    /**
     * @var array
     */
    protected $arguments;

    /**
     * @var \Magento\Mtf\TestSuite\TestSuiteFactory
     */
    protected $factory;

    /**
     * @param TestSuiteFactory $factory
     * @param array $arguments
     * @param mixed $theClass
     * @param string $name
     */
    public function __construct(
        \Magento\Mtf\TestSuite\TestSuiteFactory $factory,
        array $arguments = [],
        $theClass = '',
        $name = ''
    ) {
        $this->factory = $factory;
        $this->arguments = $arguments;
        parent::__construct($theClass, $name);
    }

    /**
     * Run callback
     *
     * @param \PHPUnit_Framework_TestResult $result
     * @return \PHPUnit_Framework_TestResult | void
     */
    public function run(\PHPUnit_Framework_TestResult $result = null)
    {
        $testClass = $this->factory->create($this->getName(), $this->arguments);
        return $testClass->run($result);
    }

    /**
     * Avoid attempt to serialize callback
     *
     * @return array
     */
    public function __sleep()
    {
        return ['arguments'];
    }
}

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

namespace Magento\Mtf\Constraint;

use Magento\Mtf\ObjectManager;

/**
 * Abstract Constraint Class
 * Constraint objects are stateful,
 * so when need to call for assertions more then one time,
 * the object should be configured again (see method "configure")
 *
 * @api
 * @abstract
 */
abstract class AbstractConstraint extends \PHPUnit_Framework_Constraint implements ConstraintInterface
{
    /**
     * Object Manager
     *
     * @var \Magento\Mtf\ObjectManager
     */
    protected $objectManager;

    /**
     * Test Case
     *
     * @var \PHPUnit_Framework_TestCase
     */
    protected $testCase;

    /**
     * Test Case Name
     *
     * @var string
     */
    protected $testCaseName = '';

    /**
     * Test Case DI Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * Assertion Result
     *
     * @var boolean
     */
    protected $result;

    /**
     * Constructor
     *
     * @constructor
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Set DI Arguments to Constraint
     *
     * @param array $arguments
     * @return void
     */
    public function configure(array $arguments = [])
    {
        $this->result = null;
        $this->arguments = $arguments;
    }

    /**
     * Get constraint result
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Get class representation is string format
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Evaluates the constraint for test case
     *
     * @param string $testCaseName
     * @return bool
     */
    protected function matches($testCaseName)
    {
        if ($this->result === null) {
            $this->testCaseName = $testCaseName;
            $this->result = true;
            $this->objectManager->invoke($this, 'processAssert', $this->arguments);
        }
        return $this->result;
    }
}

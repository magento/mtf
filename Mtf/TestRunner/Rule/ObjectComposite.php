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

namespace Mtf\TestRunner\Rule;

use Mtf\TestCase\Injectable;

/**
 * Class Rule
 *
 * @api
 */
class ObjectComposite extends AbstractRule implements ObjectRuleInterface
{
    /**
     * @var \PHPUnit_Framework_TestCase
     */
    protected $testCase;

    /**
     * Apply configuration rules to check if Test Case is eligible for execution
     *
     * @param \PHPUnit_Framework_TestCase $testCase
     * @return bool
     */
    public function apply(\PHPUnit_Framework_TestCase $testCase)
    {
        $this->testCase = $testCase;
        $result = true;
        $rules = $this->testRunnerConfig->getValue('objectRules');
        // @todo implement as standalone rule classes
        if ($rules) {
            foreach ($rules as $instanceOf => $rule) {
                if ($testCase instanceof $instanceOf) {
                    $result = $result && $this->processRule($rule);
                    if ($result === false) {
                        break;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Check whether test case is matched to specified object
     *
     * @param string $value
     * @return bool|null
     */
    protected function objectMatch($value)
    {
        if (!$this->testCase instanceof Injectable) {
            return null;
        }

        $arguments = $this->objectManager->prepareArguments($this->testCase, $this->testCase->getName(false));
        foreach ($arguments as $argument) {
            if ($argument instanceof $value) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check whether test case is injectable or regular
     *
     * @param string $value
     * @return bool
     */
    protected function typeMatch($value)
    {
        if ($this->testCase instanceof Injectable) {
            $type = 'injectable';
        } else {
            $type = 'regular';
        }
        return $type === $value;
    }
}

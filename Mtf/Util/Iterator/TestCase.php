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

namespace Mtf\Util\Iterator;

use Mtf\Util\TestClassResolver;
use Mtf\TestRunner\Rule\RuleFactory;
use Mtf\TestRunner\Rule\RuleInterface;

/**
 * Test cases iterator.
 *
 * @api
 */
class TestCase extends AbstractIterator
{
    /**
     * Resolver for collect classes of test case.
     *
     * @var TestClassResolver
     */
    protected $testClassResolver;

    /**
     * Filtering rules.
     *
     * @var RuleInterface[]
     */
    protected $rules = [];

    /**
     * @constructor
     * @param TestClassResolver $testClassResolver
     * @param RuleFactory $ruleFactory
     */
    public function __construct(
        TestClassResolver $testClassResolver,
        RuleFactory $ruleFactory
    ) {
        $this->testClassResolver = $testClassResolver;

        $this->rules[] = $ruleFactory->create('testsuite');
        $this->rules[] = $ruleFactory->create('testcase');

        $this->data = $this->collectTestCases();
        $this->initFirstElement();
    }

    /**
     * Get current element.
     *
     * @return mixed
     */
    public function current()
    {
        return $this->current;
    }

    /**
     * Check if current element is valid.
     *
     * @return boolean
     */
    protected function isValid()
    {
        $class = $this->current['class'];
        $reflection = new \ReflectionClass($class);

        if ($reflection->isAbstract()) {
            return false;
        }

        foreach ($this->rules as $rule) {
            if (!$rule->apply($class)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get list of available Test Case classes (without filtering).
     * Available keys:
     *  - class
     *  - name
     *  - path
     *
     * @return array
     */
    protected function collectTestCases()
    {
        return $this->testClassResolver->get('TestCase');
    }
}

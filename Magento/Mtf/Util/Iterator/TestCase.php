<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Util\Iterator;

use Magento\Mtf\Util\TestClassResolver;
use Magento\Mtf\TestRunner\Rule\RuleFactory;
use Magento\Mtf\TestRunner\Rule\RuleInterface;

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

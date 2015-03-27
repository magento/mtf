<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Constraint;

use Magento\Mtf\TestRunner\Rule\RuleFactory;
use Magento\Mtf\TestRunner\Rule\RuleInterface;

/**
 * Composite Constraint.
 *
 * Class for assertions composition
 * Does not perform own assertions
 *
 * @api
 */
class Composite extends AbstractConstraint
{
    /**
     * Constraint factory.
     *
     * @var ConstraintFactory
     */
    protected $factory;

    /**
     * Filtering rule.
     *
     * @var RuleInterface
     */
    protected $rule;

    /**
     * Constraint Objects.
     *
     * @var AbstractConstraint[]
     */
    protected $constraints = [];

    /**
     * Constructor.
     *
     * @constructor
     * @param ConstraintFactory $factory
     * @param RuleFactory $ruleFactory
     * @param array $codeConstraints
     */
    public function __construct(ConstraintFactory $factory, RuleFactory $ruleFactory, array $codeConstraints)
    {
        $this->factory = $factory;
        $this->rule = $ruleFactory->create('constraint');
        $this->constraints = $this->createConstraints($codeConstraints);
    }

    /**
     * Create constraints by list code.
     *
     * @param array $codes
     * @return AbstractConstraint[]
     */
    protected function createConstraints(array $codes)
    {
        $constraints = [];

        foreach ($codes as $constraintClass) {
            if (!$constraintClass) {
                continue;
            }

            if (!$this->rule->apply($constraintClass)) {
                continue;
            }

            $constraint = $this->factory->get($constraintClass);
            if ($constraint && $constraint->isActive()) {
                $constraints[] = $constraint;
            }
        }

        return $constraints;
    }

    /**
     * Set DI Arguments to Constraint.
     *
     * @param array $arguments
     * @return void
     */
    public function configure(array $arguments = [])
    {
        foreach ($this->constraints as $constraint) {
            $constraint->configure($arguments);
        }
    }

    /**
     * Evaluates the constraint for test case.
     *
     * @param string $testCaseName
     * @return bool
     */
    protected function matches($testCaseName)
    {
        $result = true;
        foreach ($this->constraints as $constraint) {
            \PHPUnit_Framework_Assert::assertThat("", $constraint);
            $result = $result && $constraint->getResult();
        }
        return $result;
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Composite Constraint (' . implode(', ', $this->constraints) . ')';
    }
}

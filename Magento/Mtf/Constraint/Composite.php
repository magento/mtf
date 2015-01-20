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

        foreach ($codes as $code) {
            if (!$code) {
                continue;
            }

            $constraintClass = $this->factory->resolveClassName($code);
            if (!$this->rule->apply($constraintClass)) {
                continue;
            }

            $constraint = $this->factory->getByCode($code);
            if ($constraint) {
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

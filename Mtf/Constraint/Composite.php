<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Constraint;

/**
 * Composite Constraint
 *
 * Class for assertions composition
 * Does not perform own assertions
 *
 * @api
 */
class Composite extends AbstractConstraint
{
    /**
     * Constraint Objects
     *
     * @var AbstractConstraint[]
     */
    protected $constraints = [];

    /**
     * Constructor
     *
     * @constructor
     * @param ConstraintFactory $factory
     * @param array $constraints
     */
    public function __construct(ConstraintFactory $factory, array $constraints)
    {
        $this->factory = $factory;

        foreach ($constraints as $code) {
            if ($code) {
                $constraint = $this->factory->getByCode($code);
                if ($constraint) {
                    $this->constraints[] = $constraint;
                }
            }
        }
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

    /**
     * Set Test Case and it's DI Arguments to Constraint
     *
     * @param array $arguments
     * @return void
     */
    public function configure(
        array $arguments = []
    ) {
        foreach ($this->constraints as $constraint) {
            $constraint->configure($arguments);
        }
    }

    /**
     * Evaluates the constraint for test case
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
}

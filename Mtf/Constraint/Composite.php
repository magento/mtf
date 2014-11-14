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
     * Set DI Arguments to Constraint
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

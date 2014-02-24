<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Constraint;

use Mtf\ObjectManager;
use Mtf\Configuration\Reader;

/**
 * Factory for Constraints
 * There is a convention around class names of Constraint classes
 * In general case constraints should be located by constraint code
 * (see method "getByCode")
 *
 * @package Mtf\Constraint
 * @api
 */
class ConstraintFactory
{
    /**
     * @var \Mtf\ObjectManager
     */
    protected $objectManager;

    /**
     * Constraints definition array
     *
     * @var array
     */
    protected $configuration;

    /**
     * @constructor
     * @param ObjectManager $objectManager
     * @param Reader $constraintReader
     */
    public function __construct(
        ObjectManager $objectManager,
        Reader $constraintReader
    ) {
        $this->objectManager = $objectManager;
        $this->configuration = $constraintReader->read('constraint');
    }

    /**
     * Get Constraint object
     *
     * @param string $class
     * @return ConstraintInterface
     * @throws \UnexpectedValueException
     */
    public function get($class)
    {
        $object = $this->objectManager->get($class);
        if (!$object instanceof ConstraintInterface) {
            throw new \UnexpectedValueException("Constraint class '$class' has to implement "
                . "\\Mtf\\Constraint\\ConstraintInterface interface.");
        }

        return $object;
    }

    /**
     * @param string $code
     * @return ConstraintInterface
     * @throws \UnexpectedValueException
     */
    public function getByCode($code)
    {
        $class = $this->resolveClassName($code);
        return $this->get($class);
    }

    /**
     * @param $code
     * @return string
     */
    protected function resolveClassName($code)
    {
        if (isset($this->configuration[$code])) {
            $class = str_replace('_', '\\', $this->configuration[$code]['module']) . '\\Test\\Constraint\\'
                . ucfirst($code);
        } else {
            $class = false;
        }

        return $class;
    }
}

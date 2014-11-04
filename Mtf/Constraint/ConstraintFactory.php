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
            throw new \UnexpectedValueException(
                sprintf('Constraint class "%s" has to implement \Mtf\Constraint\ConstraintInterface interface.', $class)
            );
        }

        return $object;
    }

    /**
     * Get class by code
     *
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
     * Resolve class name by code
     *
     * @param string $code
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

    /**
     * Return constraint configuration
     *
     * @return array
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }
}

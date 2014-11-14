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

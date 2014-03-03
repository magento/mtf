<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ObjectManager\Config\Argument\Interpreter;

use Magento\ObjectManager\Config;
use Magento\ObjectManager\Config\Argument\ObjectFactory;
use Magento\Data\Argument\InterpreterInterface;
use Magento\Stdlib\BooleanUtils;

/**
 * Interpreter that creates an instance by a type name taking into account whether it's shared or not
 */
class Object implements InterpreterInterface
{
    /**
     * @var BooleanUtils
     */
    private $booleanUtils;

    /**
     * @var ObjectFactory
     */
    private $objectFactory;

    /**
     * @param BooleanUtils $booleanUtils
     * @param ObjectFactory $objectFactory
     */
    public function __construct(BooleanUtils $booleanUtils, ObjectFactory $objectFactory)
    {
        $this->booleanUtils = $booleanUtils;
        $this->objectFactory = $objectFactory;
    }

    /**
     * {@inheritdoc}
     * @return object
     * @throws \InvalidArgumentException
     */
    public function evaluate(array $data)
    {
        if (empty($data['value'])) {
            throw new \InvalidArgumentException('Object class name is missing.');
        }
        $className = $data['value'];
        $isShared = isset($data['shared']) ? $this->booleanUtils->toBoolean($data['shared']) : null;
        $result = $this->objectFactory->create($className, $isShared);
        return $result;
    }
}

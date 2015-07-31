<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Data\Argument\Interpreter;

use Magento\Mtf\Data\Argument\InterpreterInterface;
use Magento\Mtf\Stdlib\BooleanUtils;

/**
 * Class ObjectType
 * @package Magento\Mtf\Data\Argument\Interpreter
 */
class ObjectType implements InterpreterInterface
{
    /**
     * @var \Magento\Mtf\Stdlib\BooleanUtils
     */
    protected $booleanUtils;

    /**
     * @param BooleanUtils $booleanUtils
     */
    public function __construct(BooleanUtils $booleanUtils)
    {
        $this->booleanUtils = $booleanUtils;
    }

    /**
     * Compute and return effective value of an argument
     *
     * @param array $data
     * @return mixed
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    public function evaluate(array $data)
    {
        $result = ['instance' => $data['value']];
        if (isset($data['shared'])) {
            $result['shared'] = $this->booleanUtils->toBoolean($data['shared']);
        }
        return $result;
    }
}

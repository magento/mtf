<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\Data\Argument\Interpreter;

use Magento\Mtf\Data\Argument\InterpreterInterface;
use Magento\Mtf\Data\Argument\MissingOptionalValueException;

/**
 * Interpreter that returns value of an application argument, retrieving its name from a constant
 */
class Argument implements InterpreterInterface
{
    /**
     * @var Constant
     */
    private $constInterpreter;

    /**
     * @param Constant $constInterpreter
     */
    public function __construct(Constant $constInterpreter)
    {
        $this->constInterpreter = $constInterpreter;
    }

    /**
     * {@inheritdoc}
     * @return mixed
     * @throws MissingOptionalValueException
     */
    public function evaluate(array $data)
    {
        return ['argument' => $this->constInterpreter->evaluate($data)];
    }
}

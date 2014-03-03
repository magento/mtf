<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App\Arguments;

use Magento\Data\Argument\InterpreterInterface;
use Magento\Data\Argument\Interpreter\Constant;
use Magento\Data\Argument\MissingOptionalValueException;
use Magento\App\Arguments;

/**
 * Interpreter that returns value of an application argument, retrieving its name from a constant
 */
class ArgumentInterpreter implements InterpreterInterface
{
    /**
     * @var Arguments
     */
    private $arguments;

    /**
     * @var Constant
     */
    private $constInterpreter;

    /**
     * @param Arguments $arguments
     * @param Constant $constInterpreter
     */
    public function __construct(Arguments $arguments, Constant $constInterpreter)
    {
        $this->arguments = $arguments;
        $this->constInterpreter = $constInterpreter;
    }

    /**
     * {@inheritdoc}
     * @return mixed
     * @throws MissingOptionalValueException
     */
    public function evaluate(array $data)
    {
        $argumentName = $this->constInterpreter->evaluate($data);
        $result = $this->arguments->get($argumentName);
        if ($result === null) {
            throw new MissingOptionalValueException("Value of application argument '$argumentName' is not defined.");
        }
        return $result;
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Arguments;

use Magento\Framework\Data\Argument\InterpreterInterface;
use Magento\Framework\Data\Argument\Interpreter\Constant;
use Magento\Framework\Data\Argument\MissingOptionalValueException;
use Magento\Framework\App\Arguments;

/**
 * Interpreter that returns value of an application argument, retrieving its name from a constant
 */
class ArgumentInterpreter implements InterpreterInterface
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
        return array('argument' => $this->constInterpreter->evaluate($data));
    }
}

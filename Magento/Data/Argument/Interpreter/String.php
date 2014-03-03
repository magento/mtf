<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Data\Argument\Interpreter;

use Magento\Data\Argument\InterpreterInterface;
use Magento\Stdlib\BooleanUtils;

/**
 * Interpreter of string data type that may optionally perform text translation
 */
class String implements InterpreterInterface
{
    /**
     * @var BooleanUtils
     */
    private $booleanUtils;

    /**
     * @param BooleanUtils $booleanUtils
     */
    public function __construct(BooleanUtils $booleanUtils)
    {
        $this->booleanUtils = $booleanUtils;
    }

    /**
     * {@inheritdoc}
     * @return string
     * @throws \InvalidArgumentException
     */
    public function evaluate(array $data)
    {
        if (isset($data['value'])) {
            $result = $data['value'];
            if (!is_string($result)) {
                throw new \InvalidArgumentException('String value is expected.');
            }
            $needTranslation = isset($data['translate']) ? $this->booleanUtils->toBoolean($data['translate']) : false;
            if ($needTranslation) {
                $result = __($result);
            }
        } else {
            $result = '';
        }
        return $result;
    }
}

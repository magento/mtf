<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\Data\Argument\Interpreter;

use Magento\Mtf\Data\Argument\InterpreterInterface;
use Magento\Mtf\Stdlib\BooleanUtils;

/**
 * Interpreter of string data type that may optionally perform text translation
 */
class StringType implements InterpreterInterface
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
        } else {
            $result = '';
        }
        return $result;
    }
}

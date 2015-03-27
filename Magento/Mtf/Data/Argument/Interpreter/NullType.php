<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\Data\Argument\Interpreter;

use Magento\Mtf\Data\Argument\InterpreterInterface;

/**
 * Interpreter of NULL data type
 */
class NullType implements InterpreterInterface
{
    /**
     * {@inheritdoc}
     * @return null
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function evaluate(array $data)
    {
        return null;
    }
}

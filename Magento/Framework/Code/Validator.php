<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Code;

class Validator implements ValidatorInterface
{
    /**
     * @var ValidatorInterface[]
     */
    protected $_validators = array();

    /**
     * Add validator
     *
     * @param ValidatorInterface $validator
     * @return void
     */
    public function add(ValidatorInterface $validator)
    {
        $this->_validators[] = $validator;
    }

    /**
     * Validate class
     *
     * @param string $className
     * @return bool
     * @throws \Magento\Framework\Code\ValidationException
     */
    public function validate($className)
    {
        foreach ($this->_validators as $validator) {
            $validator->validate($className);
        }
    }
}

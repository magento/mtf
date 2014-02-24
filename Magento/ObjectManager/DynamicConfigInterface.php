<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\ObjectManager;

interface DynamicConfigInterface
{
    /**
     * Map application initialization params to Object Manager configuration format
     *
     * @return array
     */
    public function getConfiguration();
}

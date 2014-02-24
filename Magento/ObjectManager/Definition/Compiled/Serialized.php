<?php
/**
 * Serialized definition reader
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\ObjectManager\Definition\Compiled;

class Serialized extends \Magento\ObjectManager\Definition\Compiled
{
    /**
     * Unpack signature
     *
     * @param string $signature
     * @return mixed
     */
    protected function _unpack($signature)
    {
        return unserialize($signature);
    }
}

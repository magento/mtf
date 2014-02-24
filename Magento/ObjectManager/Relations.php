<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\ObjectManager;

interface Relations
{
    /**
     * Check whether requested type is available for read
     *
     * @param string $type
     * @return bool
     */
    public function has($type);

    /**
     * Retrieve list of parents
     *
     * @param string $type
     * @return array
     */
    public function getParents($type);
}

<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Config;

class FileIteratorFactory
{
    public function create(\Magento\Filesystem\Directory\ReadInterface $readDirectory, $paths)
    {
        return new \Magento\Config\FileIterator($readDirectory, $paths);
    }
}

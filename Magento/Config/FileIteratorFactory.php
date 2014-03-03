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
    /**
     * Create file iterator
     *
     * @param \Magento\Filesystem\Directory\ReadInterface $readDirectory
     * @param array $paths
     * @return FileIterator
     */
    public function create(\Magento\Filesystem\Directory\ReadInterface $readDirectory, $paths)
    {
        return new \Magento\Config\FileIterator($readDirectory, $paths);
    }
}

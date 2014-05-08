<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Filesystem\File;

use Magento\Framework\Filesystem\DriverInterface;

class ReadFactory
{
    /**
     * @var \Magento\Framework\Filesystem\DriverFactory
     */
    protected $driverFactory;

    /**
     * @param \Magento\Framework\Filesystem\DriverFactory $driverFactory
     */
    public function __construct(\Magento\Framework\Filesystem\DriverFactory $driverFactory)
    {
        $this->driverFactory = $driverFactory;
    }

    /**
     * Create a readable file
     *
     * @param string $path
     * @param string|null $protocol
     * @param DriverInterface $directoryDriver [optional]
     * @return \Magento\Framework\Filesystem\File\ReadInterface
     */
    public function create($path, $protocol, DriverInterface $directoryDriver = null)
    {
        $fileDriver = $directoryDriver;
        if ($protocol) {
            $fileDriver = $this->driverFactory->get($protocol, $directoryDriver);
        }
        return new \Magento\Framework\Filesystem\File\Read($path, $fileDriver);
    }
}

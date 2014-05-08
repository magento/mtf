<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Filesystem\File;

use Magento\Framework\Filesystem\DriverInterface;

class WriteFactory
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
     * Create a readable file.
     *
     * @param string $path
     * @param string|null $protocol
     * @param DriverInterface $directoryDriver [optional]
     * @param string $mode
     * @return Write
     */
    public function create($path, $protocol, DriverInterface $directoryDriver = null, $mode = 'r')
    {
        $fileDriver = $directoryDriver;
        if ($protocol) {
            $fileDriver = $this->driverFactory->get($protocol, $directoryDriver);
        }
        return new \Magento\Framework\Filesystem\File\Write($path, $fileDriver, $mode);
    }
}

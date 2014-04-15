<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Filesystem;

class DriverFactory
{
    /**
     * @var \Magento\Framework\Filesystem\DriverInterface[]
     */
    protected $drivers = array();

    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @param DirectoryList $directoryList
     */
    public function __construct(DirectoryList $directoryList)
    {
        $this->directoryList = $directoryList;
    }

    /**
     * Get a driver instance according the given scheme.
     *
     * @param null|string $protocolCode
     * @param DriverInterface $driver
     * @return DriverInterface
     * @throws FilesystemException
     */
    public function get($protocolCode = null, DriverInterface $driver = null)
    {
        $driverClass = '\Magento\Framework\Filesystem\Driver\File';
        if ($protocolCode !== null) {
            $driverClass = $this->directoryList->getProtocolConfig($protocolCode)['driver'];
        }
        if (!isset($this->drivers[$driverClass])) {
            $this->drivers[$driverClass] = new $driverClass($driver);
            if (!$this->drivers[$driverClass] instanceof DriverInterface) {
                throw new FilesystemException("Invalid filesystem driver class: " . $driverClass);
            }
        }
        return $this->drivers[$driverClass];
    }
}

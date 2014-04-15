<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Filesystem\Directory;

class WriteFactory
{
    /**
     * Create a readable directory
     *
     * @param array $config
     * @param \Magento\Framework\Filesystem\DriverFactory $driverFactory
     * @return \Magento\Framework\Filesystem\File\WriteInterface
     */
    public function create(array $config, \Magento\Framework\Filesystem\DriverFactory $driverFactory)
    {
        $directoryDriver = isset($config['driver']) ? $config['driver'] : null;
        $driver = $driverFactory->get($directoryDriver);
        $factory = new \Magento\Framework\Filesystem\File\WriteFactory($driverFactory);

        return new \Magento\Framework\Filesystem\Directory\Write($config, $factory, $driver);
    }
}

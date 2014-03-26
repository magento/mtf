<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Filesystem\Directory;

class WriteFactory
{
    /**
     * Create a readable directory
     *
     * @param array $config
     * @param \Magento\Filesystem\DriverFactory $driverFactory
     * @return \Magento\Filesystem\File\WriteInterface
     */
    public function create(array $config, \Magento\Filesystem\DriverFactory $driverFactory)
    {
        $directoryDriver = isset($config['driver']) ? $config['driver'] : null;
        $driver = $driverFactory->get($directoryDriver);
        $factory = new \Magento\Filesystem\File\WriteFactory($driverFactory);

        return new \Magento\Filesystem\Directory\Write($config, $factory, $driver);
    }
}

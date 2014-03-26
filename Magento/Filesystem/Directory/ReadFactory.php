<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Filesystem\Directory;

class ReadFactory
{
    /**
     * Create a readable directory
     *
     * @param array $config
     * @param \Magento\Filesystem\DriverFactory $driverFactory
     * @return ReadInterface
     */
    public function create(array $config, \Magento\Filesystem\DriverFactory $driverFactory)
    {
        $directoryDriver = isset($config['driver']) ? $config['driver'] : null;
        $driver = $driverFactory->get($directoryDriver);
        $factory = new \Magento\Filesystem\File\ReadFactory($driverFactory);

        return new Read($config, $factory, $driver);
    }
}

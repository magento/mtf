<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Filesystem\Directory;

class ReadFactory
{
    /**
     * Create a readable directory
     *
     * @param array $config
     * @param \Magento\Framework\Filesystem\DriverFactory $driverFactory
     * @return ReadInterface
     */
    public function create(array $config, \Magento\Framework\Filesystem\DriverFactory $driverFactory)
    {
        $directoryDriver = isset($config['driver']) ? $config['driver'] : null;
        $driver = $driverFactory->get($directoryDriver);
        $factory = new \Magento\Framework\Filesystem\File\ReadFactory($driverFactory);

        return new Read($config, $factory, $driver);
    }
}

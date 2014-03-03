<?php
/**
 * Application primary config file resolver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Arguments\FileResolver;

class Primary implements \Magento\Config\FileResolverInterface
{
    /**
     * Module configuration file reader
     *
     * @var \Magento\Module\Dir\Reader
     */
    protected $_moduleReader;

    /**
     * @var \Magento\Filesystem\Directory\ReadInterface
     */
    protected $configDirectory;

    /**
     * @var FileIteratorFactory
     */
    protected $iteratorFactory;

    /**
     * @param \Magento\App\Filesystem $filesystem
     * @param \Magento\Config\FileIteratorFactory $iteratorFactory
     */
    public function __construct(
        \Magento\App\Filesystem $filesystem,
        \Magento\Config\FileIteratorFactory $iteratorFactory
    ) {
        $this->configDirectory = $filesystem->getDirectoryRead(\Magento\App\Filesystem::CONFIG_DIR);
        $this->iteratorFactory = $iteratorFactory;
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function get($filename, $scope)
    {
        return $this->iteratorFactory->create(
            $this->configDirectory,
            $this->configDirectory->search('{*' . $filename . ',*/*' . $filename . '}')
        );
    }
}

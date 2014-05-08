<?php
/**
 * Local Application configuration loader (app/etc/local.xml)
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Arguments;

class Loader
{
    /**
     * Local configuration file
     */
    const PARAM_CUSTOM_FILE = 'custom.options.file';

    /**
     * Local configuration file
     */
    const LOCAL_CONFIG_FILE = 'local.xml';

    /**
     * Directory registry
     *
     * @var string
     */
    protected $_dir;

    /**
     * Custom config file
     *
     * @var string
     */
    protected $_customFile;

    /**
     * Configuration identifier attributes
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/config/resource' => 'name',
        '/config/connection' => 'name',
        '/config/cache/frontend' => 'name',
        '/config/cache/type' => 'name'
    );

    /**
     * @param \Magento\Framework\App\Filesystem\DirectoryList $dirList
     * @param string $customFile
     */
    public function __construct(\Magento\Framework\App\Filesystem\DirectoryList $dirList, $customFile = null)
    {
        $this->_dir = $dirList->getDir(\Magento\Framework\App\Filesystem::CONFIG_DIR);
        $this->_customFile = $customFile;
    }

    /**
     * Load configuration
     *
     * @return array
     */
    public function load()
    {
        $localConfig = new \Magento\Framework\Config\Dom('<config/>', $this->_idAttributes);

        $localConfigFile = $this->_dir . '/' . self::LOCAL_CONFIG_FILE;
        if (file_exists($localConfigFile)) {
            // 1. app/etc/local.xml
            $localConfig->merge(file_get_contents($localConfigFile));

            // 2. app/etc/<dir>/<file>.xml
            if (preg_match('/^[a-z\d_-]+(\/|\\\)+[a-z\d_-]+\.xml$/', $this->_customFile)) {
                $localConfigExtraFile = $this->_dir . '/' . $this->_customFile;
                $localConfig->merge(file_get_contents($localConfigExtraFile));
            }
        }

        $arrayNodeConfig = new \Magento\Framework\Config\Dom\ArrayNodeConfig(
            new \Magento\Framework\Config\Dom\NodePathMatcher(),
            $this->_idAttributes
        );
        $converter = new \Magento\Framework\Config\Converter\Dom\Flat($arrayNodeConfig);

        $result = $converter->convert($localConfig->getDom());
        return !empty($result['config']) ? $result['config'] : array();
    }
}

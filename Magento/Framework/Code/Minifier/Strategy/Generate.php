<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Minification strategy that generates minified file, if it does not exist or outdated
 */
namespace Magento\Framework\Code\Minifier\Strategy;

use Magento\Framework\Filesystem\Directory\Read;
use Magento\Framework\Filesystem\Directory\Write;

class Generate implements \Magento\Framework\Code\Minifier\StrategyInterface
{
    /**
     * @var \Magento\Framework\Code\Minifier\AdapterInterface
     */
    protected $adapter;

    /**
     * @var Read
     */
    protected $rootDirectory;

    /**
     * @var Write
     */
    protected $pubViewCacheDir;

    /**
     * @param \Magento\Framework\Code\Minifier\AdapterInterface $adapter
     * @param \Magento\Framework\App\Filesystem $filesystem
     */
    public function __construct(\Magento\Framework\Code\Minifier\AdapterInterface $adapter, \Magento\Framework\App\Filesystem $filesystem)
    {
        $this->adapter = $adapter;
        $this->rootDirectory = $filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem::ROOT_DIR);
        $this->pubViewCacheDir = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem::PUB_VIEW_CACHE_DIR);
    }

    /**
     * Get path to minified file for specified original file
     *
     * @param string $originalFile path to original file relative to pub/view_cache
     * @param string $targetFile path relative to pub/view_cache
     * @return void
     */
    public function minifyFile($originalFile, $targetFile)
    {
        if ($this->_isUpdateNeeded($originalFile, $targetFile)) {
            $content = $this->rootDirectory->readFile($originalFile);
            $content = $this->adapter->minify($content);
            $targetFile = $this->pubViewCacheDir->getRelativePath($targetFile);
            $this->pubViewCacheDir->writeFile($targetFile, $content);
            $this->pubViewCacheDir->touch($targetFile, $this->rootDirectory->stat($originalFile)['mtime']);
        }
    }

    /**
     * Check whether minified file should be created/updated
     *
     * @param string $originalFile path to original file relative to pub/view_cache
     * @param string $minifiedFile path relative to pub/view_cache
     * @return bool
     */
    protected function _isUpdateNeeded($originalFile, $minifiedFile)
    {
        if (!$this->pubViewCacheDir->isExist($minifiedFile)) {
            return true;
        }
        $originalFileMtime = $this->rootDirectory->stat($originalFile)['mtime'];
        $minifiedFileMtime = $this->pubViewCacheDir->stat($minifiedFile)['mtime'];
        return $originalFileMtime != $minifiedFileMtime;
    }
}

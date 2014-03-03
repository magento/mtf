<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Code;

use Magento\Filesystem\Directory\Read;

class Minifier
{
    /**
     * @var \Magento\Code\Minifier\StrategyInterface
     */
    private $_strategy;

    /**
     * @var Read
     */
    private $rootDirectory;

    /**
     * @var string directory name where minified files are saved
     */
    private $directoryName;

    /**
     * @var Read
     */
    private $pubViewCacheDir;

    /**
     * @param \Magento\Code\Minifier\StrategyInterface $strategy
     * @param \Magento\App\Filesystem $filesystem
     * @param string $directoryName
     */
    public function __construct(
        \Magento\Code\Minifier\StrategyInterface $strategy,
        \Magento\App\Filesystem $filesystem,
        $directoryName
    ) {
        $this->_strategy = $strategy;
        $this->rootDirectory = $filesystem->getDirectoryRead(\Magento\App\Filesystem::ROOT_DIR);
        $this->pubViewCacheDir = $filesystem->getDirectoryRead(\Magento\App\Filesystem::PUB_VIEW_CACHE_DIR);
        $this->directoryName = $directoryName;
    }

    /**
     * Get path to minified file
     *
     * @param string $originalFile
     * @return bool|string
     */
    public function getMinifiedFile($originalFile)
    {
        if ($this->_isFileMinified($originalFile)) {
            return $originalFile;
        }
        $originalFileRelative = $this->rootDirectory->getRelativePath($originalFile);
        $minifiedFile = $this->_findOriginalMinifiedFile($originalFileRelative);
        if (!$minifiedFile) {
            $minifiedFile = $this->directoryName . '/' . $this->_generateMinifiedFileName($originalFile);
            $this->_strategy->minifyFile($originalFileRelative, $minifiedFile);
        }

        $minifiedFile = $this->pubViewCacheDir->getRelativePath($minifiedFile);
        return $this->pubViewCacheDir->getAbsolutePath($minifiedFile);
    }

    /**
     * Check if file is minified
     *
     * @param string $fileName
     * @return bool
     */
    protected function _isFileMinified($fileName)
    {
        return (bool)preg_match('#.min.\w+$#', $fileName);
    }

    /**
     * Generate name of the minified file
     *
     * @param string $originalFile
     * @return string
     */
    protected function _generateMinifiedFileName($originalFile)
    {
        $fileInfo = pathinfo($originalFile);
        $minifiedName = md5($originalFile) . '_' . $fileInfo['filename'] . '.min.' . $fileInfo['extension'];

        return $minifiedName;
    }

    /**
     * Search for minified file provided along with the original file in the code base
     *
     * @param string $originalFile
     * @return bool|string
     */
    protected function _findOriginalMinifiedFile($originalFile)
    {
        $fileInfo = pathinfo($originalFile);
        $minifiedFile = $fileInfo['dirname'] . '/' . $fileInfo['filename'] . '.min.' . $fileInfo['extension'];
        if ($this->rootDirectory->isExist($minifiedFile)) {
            return $minifiedFile;
        }
        return false;
    }
}

<?php
/**
 * The class, which verifies existence and write access to the needed application directories
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Filesystem\DirectoryList;

use Magento\App\State;
use Magento\BootstrapException;
use Magento\App\Filesystem;
use Magento\Filesystem\FilesystemException;

class Verification
{
    /**
     * Codes of directories to create and verify in production mode
     *
     * @var string[]
     */
    protected static $productionDirs = array(
        Filesystem::SESSION_DIR,
        Filesystem::CACHE_DIR,
        Filesystem::LOG_DIR,
    );

    /**
     * Codes of directories to create and verify in non-production mode
     *
     * @var string[]
     */
    protected static $nonProductionDirs = array(
        Filesystem::SESSION_DIR,
        Filesystem::CACHE_DIR,
        Filesystem::LOG_DIR,
    );

    /**
     * @var \Magento\App\Filesystem
     */
    protected $filesystem;

    /**
     * Cached list of directories to create and verify write access
     *
     * @var string[]
     */
    protected $dirsToVerify = array();

    /**
     * Constructor - initialize object with required dependencies, determine application state
     *
     * @param Filesystem $filesystem
     * @param State $appState
     */
    public function __construct(Filesystem $filesystem, State $appState)
    {
        $this->filesystem = $filesystem;
        $this->dirsToVerify = $this->_getDirsToVerify($appState);
    }

    /**
     * Return list of directories, that must be verified according to the application mode
     *
     * @param State $appState
     * @return string[]
     */
    protected function _getDirsToVerify(State $appState)
    {
        $codes = ($appState->getMode() == State::MODE_PRODUCTION)
            ? self::$productionDirs
            : self::$nonProductionDirs;
        return $codes;
    }

    /**
     * Create the required directories, if they don't exist, and verify write access for existing directories
     *
     * @return void
     * @throws BootstrapException
     *
     */
    public function createAndVerifyDirectories()
    {
        $fails = array();
        foreach ($this->dirsToVerify as $code) {
            $directory = $this->filesystem->getDirectoryWrite($code);
            if ($directory->isExist()) {
                if (!$directory->isWritable()) {
                    $fails[] = $directory->getAbsolutePath();
                }
            } else {
                try {
                    $directory->create();
                } catch (FilesystemException $e) {
                    $fails[] = $directory->getAbsolutePath();
                }
            }
        }

        if ($fails) {
            $dirList = implode(', ', $fails);
            throw new BootstrapException(
                "Cannot create or verify write access: {$dirList}"
            );
        }
    }
}

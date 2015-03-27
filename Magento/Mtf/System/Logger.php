<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\System;

use Magento\Mtf\Config\DataInterface;

/**
 * Class for working with log directory and file
 */
class Logger implements LoggerInterface
{
    /**
     * Log directory parameter key
     */
    const LOG_DIR_PARAM = 'log_directory';

    /**
     * Path to log directory
     *
     * @var string
     */
    protected $logDirectoryPath;

    /**
     * Constructor
     *
     * @param DataInterface $config
     * @param null $customLogDirectory
     */
    public function __construct(DataInterface $config, $customLogDirectory = null)
    {
        $logDirectoryFallback = [
            $customLogDirectory,
            $_ENV[static::LOG_DIR_PARAM],
            $this->getRootPath()
        ];

        $this->resolveLogDirectory($logDirectoryFallback);
    }

    /**
     * Set log directory
     *
     * @param string $dirPath
     * @return $this
     */
    public function setLogDirectoryPath($dirPath)
    {
        if (strpos($dirPath, MTF_BP) === false) {
            $dirPath = MTF_BP . '/' . $dirPath;
        }
        $this->checkDirectory($dirPath);
        $this->logDirectoryPath = rtrim($dirPath, '\/');
        return $this;
    }

    /**
     * Retrieve absolute path to log directory
     *
     * @return string
     */
    public function getLogDirectoryPath()
    {
        return $this->logDirectoryPath;
    }

    /**
     * Set appropriate log directories
     *
     * @param array $dirs
     * @return void
     */
    protected function resolveLogDirectory(array $dirs)
    {
        foreach ($dirs as $dir) {
            if (!$dir) {
                continue;
            }
            $this->setLogDirectoryPath($dir);
            break;
        }
    }

    /**
     * Create log directory if not exists
     *
     * @param string $path
     * @return void
     * @throws \RuntimeException
     */
    protected function checkDirectory($path)
    {
        $result = true;
        if (!is_dir($path)) {
            $result = mkdir($path, 0777, true);
        }
        if (!$result) {
            throw new \RuntimeException('Directory path ' . $path . ' does not exist');
        }
    }

    /**
     * Get root path of the directory
     *
     * @return string
     */
    protected function getRootPath()
    {
        $rootPath = dirname(dirname(__DIR__));
        return str_replace('\\', '/', $rootPath);
    }

    /**
     * Log message into log file
     *
     * @param string $message
     * @param string $filename
     * @param int $context
     * @throws \Exception
     * @return int
     */
    public function log($message, $filename, $context = FILE_APPEND)
    {
        return file_put_contents($this->getLogDirectoryPath() . '/' . $filename, $message, $context);
    }
}

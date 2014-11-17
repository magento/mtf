<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Mtf\System;

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
     * @param Config $config
     * @param null $customLogDirectory
     */
    public function __construct(\Mtf\System\Config $config, $customLogDirectory = null)
    {
        $logDirectoryFallback = [
            $customLogDirectory,
            $config->getEnvironmentValue(static::LOG_DIR_PARAM),
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
        $this->checkDirectory($dirPath);
        $this->logDirectoryPath = rtrim($dirPath, '\/');
        return $this;
    }

    /**
     * Getter for logDirectoryPath
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
     * @throws \RuntimeException
     */
    protected function checkDirectory($path)
    {
        $result = true;
        if (!is_dir($path)) {
            $result = mkdir($path, 0777, true);
        }
        if (!$result) {
            throw new \RuntimeException('Directory path ' . $path . ' not exists');
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
        return file_put_contents(MTF_BP . '/' . $this->getLogDirectoryPath() . '/' . $filename, $message, $context);
    }
}

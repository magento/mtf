<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Mtf\System;

class Logger implements \Mtf\System\LoggerInterface
{
    /**
     * Log directory parameter key
     */
    const LOG_DIR_PARAM = 'log_directory';

    /**
     * @var string
     */
    protected $message;

    /**
     * @var string
     */
    protected $filepath;

    /**
     * @var string
     */
    protected $logDirectoryPath;

    /**
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
     * @return string
     */
    public function getLogDirectoryPath()
    {
        return $this->logDirectoryPath;
    }

    /**
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
     * @param $path
     * @throws \RuntimeException
     */
    protected function checkDirectory($path)
    {
        $result = true;
        $path = realpath($this->getRootPath() . '/' . $path);
        if (!is_dir($path)) {
            $result = mkdir($path, 0777, true);
        }
        if (!$result) {
            throw new \RuntimeException('Directory path ' . $path . ' not exists');
        }
    }

    /**
     * @return string
     */
    protected function getRootPath()
    {
        $rootPath = dirname(dirname(__DIR__));
        return str_replace('\\', '/', $rootPath);
    }

    /**
     * @param string $message
     * @param string $filename
     * @param int $context
     * @throws \Exception
     * @return int
     */
    public function log($message, $filename, $context = FILE_APPEND)
    {
        $filePath = $this->getRootPath() . '/' . $this->getLogDirectoryPath() . '/' . ltrim($filename, '\/');
        return file_put_contents($filePath, $message, $context);
    }
}

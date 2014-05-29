<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Mtf\System;

interface LoggerInterface
{
    /**
     * @param string $message
     * @param string $filename
     * @param null $context
     * @return int
     */
    public function log($message, $filename, $context = null);

    /**
     * @return string
     */
    public function getLogDirectoryPath();

    /**
     * @param string $logDirectoryPath
     * @return mixed
     */
    public function setLogDirectoryPath($logDirectoryPath);
}

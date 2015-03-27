<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\System;

/**
 * Interface LoggerInterface
 * @package Magento\Mtf\System
 */
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
     * @return $this
     */
    public function setLogDirectoryPath($logDirectoryPath);
}

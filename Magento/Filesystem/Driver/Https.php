<?php
/**
 * Origin filesystem driver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Filesystem\Driver;

use Magento\Filesystem\FilesystemException;

/**
 * Class Https
 *
 * @package Magento\Filesystem\Driver
 */
class Https extends Http
{
    /**
     * @var string
     */
    protected $scheme = 'https';
}

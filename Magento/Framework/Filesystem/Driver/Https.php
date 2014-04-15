<?php
/**
 * Origin filesystem driver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Filesystem\Driver;

/**
 * Class Https
 *
 * @package Magento\Framework\Filesystem\Driver
 */
class Https extends Http
{
    /**
     * @var string
     */
    protected $scheme = 'https';
}

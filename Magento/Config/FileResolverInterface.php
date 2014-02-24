<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Config;

interface FileResolverInterface
{
    /**
     * Retrieve the list of configuration files with given name that relate to specified scope
     *
     * @param string $filename
     * @param string $scope
     * @return array
     */
    public function get($filename, $scope);
}

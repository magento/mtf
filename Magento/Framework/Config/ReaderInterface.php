<?php
/**
 * Reader responsible for retrieving provided scope of configuration from storage
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Framework\Config;

interface ReaderInterface
{
    /**
     * Read configuration scope
     *
     * @param string|null $scope
     * @return array
     */
    public function read($scope = null);
}

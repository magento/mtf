<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\ObjectManager\ConfigLoader;

/**
 * Class Module
 * MTF Modular configuration loader. Loads configuration from XML files, located in modules' directories
 *
 * @package Mtf\ObjectManager\ConfigLoader
 * @internal
 */
class Module
{
    /**
     * Framework mode
     *
     * @var string
     */
    protected $_appMode = 'developer';

    /**
     * Load MTF configuration from .xml files
     *
     * @param null $scope
     * @return array
     */
    public function load($scope = null)
    {
        $reader = new \Mtf\ObjectManager\Config\Reader\Dom(
            new \Mtf\Config\FileResolver\Module(),
            new \Magento\ObjectManager\Config\Mapper\Dom(
                new \Magento\Stdlib\BooleanUtils(),
                new \Magento\ObjectManager\Config\Mapper\ArgumentParser()
            ),
            new \Magento\ObjectManager\Config\SchemaLocator(),
            new \Mtf\Config\ValidationState($this->_appMode)
        );

        return $reader->read($scope);
    }
}

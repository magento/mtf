<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\ObjectManager\ConfigLoader;

/**
 * Class Primary
 * MTF Primary DI configuration loader
 *
 * @package Mtf\ObjectManager\ConfigLoader
 * @internal
 */
class Primary
{
    /**
     * Framework mode
     *
     * @var string
     */
    protected $_appMode = 'developer';

    /**
     * Load MTF primary DI configuration
     *
     * @return array
     */
    public function load()
    {
        $reader = new \Mtf\ObjectManager\Config\Reader\Dom(
            new \Mtf\Config\FileResolver\Primary(),
            new \Magento\ObjectManager\Config\Mapper\Dom(),
            new \Mtf\ObjectManager\Config\SchemaLocator(),
            new \Mtf\Config\ValidationState($this->_appMode)
        );

        return $reader->read();
    }
}

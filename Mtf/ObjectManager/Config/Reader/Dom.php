<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\ObjectManager\Config\Reader;

/**
 * Class Dom
 * @package Mtf\ObjectManager\Config\Reader
 * @internal
 */
class Dom extends \Magento\Config\Reader\Filesystem
{
    /**
     * List of paths to identifiable nodes
     *
     * @var array
     */
    protected $_idAttributes = [
        '/config/preference'         => 'for',
        '/config/type'               => 'name',
        '/config/type/param'         => 'name',
        '/config/type/plugin'        => 'name',
        '/config/virtualType'        => 'name',
        '/config/virtualType/param'  => 'name',
        '/config/virtualType/plugin' => 'name',
    ];

    /**
     * @constructor
     * @param \Magento\Config\FileResolverInterface $fileResolver
     * @param \Magento\ObjectManager\Config\Mapper\Dom $converter
     * @param \Magento\ObjectManager\Config\SchemaLocator $schemaLocator
     * @param \Magento\Config\ValidationStateInterface $validationState
     * @param array $idAttributes
     * @param string $filename
     * @param string $domDocumentClass
     */
    public function __construct(
        \Magento\Config\FileResolverInterface $fileResolver,
        \Magento\ObjectManager\Config\Mapper\Dom $converter,
        \Magento\ObjectManager\Config\SchemaLocator $schemaLocator,
        \Magento\Config\ValidationStateInterface $validationState,
        $idAttributes = [],
        $filename = 'di.xml',
        $domDocumentClass = 'Magento\Config\Dom'
    ) {
        parent::__construct(
            $fileResolver, $converter, $schemaLocator, $validationState, $filename, $idAttributes, $domDocumentClass
        );
    }
}

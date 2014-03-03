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
     * Name of an attribute that stands for data type of node values
     */
    const TYPE_ATTRIBUTE = 'xsi:type';

    /**
     * @var array
     */
    protected $_idAttributes = [
        '/config/preference'                                    => 'for',
        '/config/(type|virtualType)'                            => 'name',
        '/config/(type|virtualType)/plugin'                     => 'name',
        '/config/(type|virtualType)/arguments/argument'         => 'name',
        '/config/(type|virtualType)/arguments/argument(/item)+' => 'name',
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

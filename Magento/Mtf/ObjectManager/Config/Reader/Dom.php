<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\ObjectManager\Config\Reader;

/**
 * Class Dom
 *
 * @internal
 */
class Dom extends \Magento\Mtf\Config\Reader\Filesystem
{
    /**
     * Name of an attribute that stands for data type of node values
     */
    const TYPE_ATTRIBUTE = 'xsi:type';

    /**
     * @param \Magento\Mtf\Config\FileResolverInterface $fileResolver
     * @param \Magento\Mtf\ObjectManager\Config\Mapper\Dom $converter
     * @param \Magento\Mtf\ObjectManager\Config\SchemaLocator $schemaLocator
     * @param \Magento\Mtf\Config\ValidationStateInterface $validationState
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     * @param string $defaultScope
     */
    public function __construct(
        \Magento\Mtf\Config\FileResolverInterface $fileResolver,
        \Magento\Mtf\ObjectManager\Config\Mapper\Dom $converter,
        \Magento\Mtf\ObjectManager\Config\SchemaLocator $schemaLocator,
        \Magento\Mtf\Config\ValidationStateInterface $validationState,
        $fileName = 'di.xml',
        $idAttributes = [
            '/config/preference' => 'for',
            '/config/(type|virtualType)' => 'name',
            '/config/(type|virtualType)/plugin' => 'name',
            '/config/(type|virtualType)/arguments/argument' => 'name',
            '/config/(type|virtualType)/arguments/argument(/item)+' => 'name'
        ],
        $domDocumentClass = 'Magento\Mtf\Config\Dom',
        $defaultScope = 'etc'
    ) {
        parent::__construct(
            $fileResolver,
            $converter,
            $schemaLocator,
            $validationState,
            $fileName,
            $idAttributes,
            $domDocumentClass,
            $defaultScope
        );
    }

    /**
     * Create and return a config merger instance that takes into account types of arguments
     *
     * @param string $mergerClass
     * @param string $initialContents
     * @return \Magento\Mtf\Config\Dom
     */
    protected function _createConfigMerger($mergerClass, $initialContents)
    {
        return new $mergerClass($initialContents, $this->_idAttributes, self::TYPE_ATTRIBUTE, $this->_perFileSchema);
    }
}

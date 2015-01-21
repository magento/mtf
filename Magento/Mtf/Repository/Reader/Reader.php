<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Mtf\Repository\Reader;

use \Mtf\Repository\Reader\FileResolver\Module;

/**
 * Module declaration reader. Reads repository.xml declaration files from module /Repository directories.
 */
class Reader extends \Magento\Framework\Config\Reader\Filesystem
{
    /**
     * List of name attributes for merge.
     *
     * @var array
     */
    protected $_idAttributes = [
        '/repository/storage' => 'class',
        '/repository/storage/dataset' => 'name',
        '/repository/storage/dataset/field' => 'name|path',
        '/repository/storage/dataset/field(/item)+' => 'name|label',
    ];

    /**
     * @constructor
     * @param Module $fileResolver
     * @param Converter $converter
     * @param SchemaLocator $schemaLocator
     * @param ValidationState $validationState
     * @param string $fileName [optional]
     * @param array $idAttributes [optional]
     * @param string $domDocumentClass [optional]
     * @param string $defaultScope [optional]
     */
    public function __construct(
        Module $fileResolver,
        Converter $converter,
        SchemaLocator $schemaLocator,
        ValidationState $validationState,
        $fileName = '*.xml',
        $idAttributes = [],
        $domDocumentClass = 'Mtf\Repository\Reader\Dom',
        $defaultScope = 'Repository'
    ) {
        $this->_fileResolver = $fileResolver;
        $this->_converter = $converter;
        $this->_fileName = $fileName;
        $this->_idAttributes = array_replace($this->_idAttributes, $idAttributes);
        $this->_schemaFile = $schemaLocator->getSchema();
        $this->_isValidated = $validationState->isValidated();
        $this->_perFileSchema = $schemaLocator->getPerFileSchema() &&
        $this->_isValidated ? $schemaLocator->getPerFileSchema() : null;
        $this->_domDocumentClass = $domDocumentClass;
        $this->_defaultScope = $defaultScope;
    }
}

<?php
/**
 * Filesystem configuration loader. Loads configuration from XML files, split by scopes
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 *
 */
namespace Magento\Config\Reader;

/**
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class Filesystem implements \Magento\Config\ReaderInterface
{
    /**
     * File locator
     *
     * @var \Magento\Config\FileResolverInterface
     */
    protected $_fileResolver;

    /**
     * Config converter
     *
     * @var \Magento\Config\ConverterInterface
     */
    protected $_converter;

    /**
     * The name of file that stores configuration
     *
     * @var string
     */
    protected $_fileName;

    /**
     * Path to corresponding XSD file with validation rules for merged config
     *
     * @var string
     */
    protected $_schema;

    /**
     * Path to corresponding XSD file with validation rules for separate config files
     *
     * @var string
     */
    protected $_perFileSchema;

    /**
     * List of id attributes for merge
     *
     * @var array
     */
    protected $_idAttributes = array();

    /**
     * Class of dom configuration document used for merge
     *
     * @var string
     */
    protected $_domDocumentClass;

    /**
     * Should configuration be validated
     *
     * @var bool
     */
    protected $_isValidated;

    /**
     * @param \Magento\Config\FileResolverInterface $fileResolver
     * @param \Magento\Config\ConverterInterface $converter
     * @param \Magento\Config\SchemaLocatorInterface $schemaLocator
     * @param \Magento\Config\ValidationStateInterface $validationState
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     * @param string $defaultScope
     */
    public function __construct(
        \Magento\Config\FileResolverInterface $fileResolver,
        \Magento\Config\ConverterInterface $converter,
        \Magento\Config\SchemaLocatorInterface $schemaLocator,
        \Magento\Config\ValidationStateInterface $validationState,
        $fileName,
        $idAttributes = array(),
        $domDocumentClass = 'Magento\Config\Dom',
        $defaultScope = 'global'
    ) {
        $this->_fileResolver = $fileResolver;
        $this->_converter = $converter;
        $this->_fileName = $fileName;
        $this->_idAttributes = array_replace($this->_idAttributes, $idAttributes);
        $this->_schemaFile = $schemaLocator->getSchema();
        $this->_isValidated = $validationState->isValidated();
        $this->_perFileSchema = $schemaLocator->getPerFileSchema() && $this->_isValidated
            ? $schemaLocator->getPerFileSchema()
            : null;
        $this->_domDocumentClass = $domDocumentClass;
        $this->_defaultScope = $defaultScope;
    }

    /**
     * Load configuration scope
     *
     * @param string|null $scope
     * @return array
     * @throws \Magento\Exception
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function read($scope = null)
    {
        $scope = $scope ?: $this->_defaultScope;
        $fileList = $this->_fileResolver->get($this->_fileName, $scope);
        if (!count($fileList)) {
            return array();
        }
        $output = $this->_readFiles($fileList);

        return $output;
    }

    /**
     * @param array $fileList
     * @return array
     * @throws \Magento\Exception
     */
    protected function _readFiles($fileList)
    {
        /** @var \Magento\Config\Dom $domDocument */
        $domDocument = null;
        foreach ($fileList as $key => $content) {
            try {
                if (is_null($domDocument)) {
                    $class = $this->_domDocumentClass;
                    $domDocument = new $class(
                        $content,
                        $this->_idAttributes,
                        $this->_perFileSchema
                    );
                } else {
                    $domDocument->merge($content);
                }
            } catch (\Magento\Config\Dom\ValidationException $e) {
                throw new \Magento\Exception("Invalid XML in file " . $key . ":\n" . $e->getMessage());
            }
        }
        if ($this->_isValidated) {
            $errors = array();
            if ($domDocument && !$domDocument->validate($this->_schemaFile, $errors)) {
                $message = "Invalid Document \n";
                throw new \Magento\Exception($message . implode("\n", $errors));
            }
        }

        $output = array();
        if ($domDocument) {
            $output = $this->_converter->convert($domDocument->getDom());
        }
        return $output;
    }
}

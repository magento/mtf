<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Config\Reader;

/**
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class Filesystem implements \Magento\Mtf\Config\ReaderInterface
{
    /**
     * File locator.
     *
     * @var \Magento\Mtf\Config\FileResolverInterface
     */
    protected $_fileResolver;

    /**
     * Config converter.
     *
     * @var \Magento\Mtf\Config\ConverterInterface
     */
    protected $_converter;

    /**
     * The name of file that stores configuration.
     *
     * @var string
     */
    protected $_fileName;

    /**
     * Path to corresponding XSD file with validation rules for merged config.
     *
     * @var string
     */
    protected $_schema;

    /**
     * Path to corresponding XSD file with validation rules for separate config files.
     *
     * @var string
     */
    protected $_perFileSchema;

    /**
     * List of id attributes for merge.
     *
     * @var array
     */
    protected $_idAttributes;

    /**
     * Class of dom configuration document used for merge.
     *
     * @var string
     */
    protected $_domDocumentClass;

    /**
     * Should configuration be validated.
     *
     * @var bool
     */
    protected $_isValidated;

    /**
     * Config replacer.
     *
     * @var \Magento\Mtf\Config\ReplacerInterface
     */
    protected $replacer;

    /**
     * @constructor
     * @param \Magento\Mtf\Config\FileResolverInterface $fileResolver
     * @param \Magento\Mtf\Config\ConverterInterface $converter
     * @param \Magento\Mtf\Config\SchemaLocatorInterface $schemaLocator
     * @param \Magento\Mtf\Config\ValidationStateInterface $validationState
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     * @param string $defaultScope
     * @param \Magento\Mtf\Config\ReplacerInterface $replacer
     */
    public function __construct(
        \Magento\Mtf\Config\FileResolverInterface $fileResolver,
        \Magento\Mtf\Config\ConverterInterface $converter,
        \Magento\Mtf\Config\SchemaLocatorInterface $schemaLocator,
        \Magento\Mtf\Config\ValidationStateInterface $validationState,
        $fileName,
        $idAttributes = [],
        $domDocumentClass = 'Magento\Mtf\Config\Dom',
        $defaultScope = 'global',
        \Magento\Mtf\Config\ReplacerInterface $replacer = null
    ) {
        $this->_fileResolver = $fileResolver;
        $this->_converter = $converter;
        $this->_fileName = $fileName;
        $this->_idAttributes = $idAttributes;
        $this->_schemaFile = $schemaLocator->getSchema();
        $this->_isValidated = $validationState->isValidated();
        $this->_perFileSchema = $schemaLocator->getPerFileSchema() &&
        $this->_isValidated ? $schemaLocator->getPerFileSchema() : null;
        $this->_domDocumentClass = $domDocumentClass;
        $this->_defaultScope = $defaultScope;
        $this->replacer = $replacer;
    }

    /**
     * Load configuration scope.
     *
     * @param string|null $scope
     * @return array
     * @throws \Exception
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function read($scope = null)
    {
        $scope = $scope ? : $this->_defaultScope;
        $fileList = $this->_fileResolver->get($this->_fileName, $scope);
        if (!count($fileList)) {
            return [];
        }
        $output = $this->_readFiles($fileList);

        return $output;
    }

    /**
     * Set name of the config file.
     *
     * @param string $fileName
     */
    public function setFileName($fileName)
    {
        $this->_fileName = $fileName;
    }

    /**
     * Read configuration files.
     *
     * @param array $fileList
     * @return array
     * @throws \Exception
     */
    protected function _readFiles($fileList)
    {
        /** @var \Magento\Mtf\Config\Dom $configMerger */
        $configMerger = null;
        foreach ($fileList as $key => $content) {
            try {
                if (!$configMerger) {
                    $configMerger = $this->_createConfigMerger($this->_domDocumentClass, $content);
                } else {
                    $configMerger->merge($content);
                }
            } catch (\Magento\Mtf\Config\Dom\ValidationException $e) {
                throw new \Exception("Invalid XML in file " . $key . ":\n" . $e->getMessage());
            }
        }
        if ($this->_isValidated) {
            $errors = [];
            if ($configMerger && !$configMerger->validate($this->_schemaFile, $errors)) {
                $message = "Invalid Document \n";
                throw new \Exception($message . implode("\n", $errors));
            }
        }

        $output = [];
        if ($configMerger) {
            $output = $this->_converter->convert($configMerger->getDom());
            if ($this->replacer !== null) {
                $this->replacer->apply($output);
            }
        }
        return $output;
    }

    /**
     * Return newly created instance of a config merger.
     *
     * @param string $mergerClass
     * @param string $initialContents
     * @return \Magento\Mtf\Config\Dom
     * @throws \UnexpectedValueException
     */
    protected function _createConfigMerger($mergerClass, $initialContents)
    {
        $result = new $mergerClass($initialContents, $this->_idAttributes, null, $this->_perFileSchema);
        if (!$result instanceof \Magento\Mtf\Config\Dom) {
            throw new \UnexpectedValueException(
                "Instance of the DOM config merger is expected, got {$mergerClass} instead."
            );
        }
        return $result;
    }
}

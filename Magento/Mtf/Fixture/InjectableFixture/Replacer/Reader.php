<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Fixture\InjectableFixture\Replacer;

/**
 * Reader for replacing values.
 */
class Reader
{
    /**
     * Converter data to array.
     *
     * @var \Magento\Mtf\Config\ConverterInterface
     */
    protected $converter;

    /**
     * Path to corresponding XSD file with validation rules.
     *
     * @var string
     */
    protected $schema;

    /**
     * Class of dom document.
     *
     * @var string
     */
    protected $domDocumentClass;

    /**
     * @constructor
     * @param Converter $converter
     * @param SchemaLocator $schemaLocator
     * @param string $domDocumentClass
     */
    public function __construct(
        Converter $converter,
        SchemaLocator $schemaLocator,
        $domDocumentClass = 'Magento\Mtf\Config\Dom'
    ) {
        $this->converter = $converter;
        $this->schema = $schemaLocator->getSchema();
        $this->domDocumentClass = $domDocumentClass;
    }

    /**
     * Read data from file.
     *
     * @param string $filePath
     * @return array
     * @throws \Exception
     */
    public function read($filePath)
    {
        $content = file_get_contents($filePath);
        if (false === $content) {
            throw new \Exception("Can't read file: " . $filePath);
        }

        $document = $this->createDocument($this->domDocumentClass, $content);
        if ($this->schema) {
            $errors = [];
            if (!$document->validate($this->schema, $errors)) {
                $message = "Invalid Document \n";
                throw new \Exception($message . implode("\n", $errors));
            }
        }

        return $this->converter->convert($document->getDom());
    }

    /**
     * Return newly created instance of a data document.
     *
     * @param string $class
     * @param string $initialContents
     * @return \Magento\Mtf\Config\Dom
     * @throws \UnexpectedValueException
     */
    protected function createDocument($class, $initialContents)
    {
        $result = new $class($initialContents);
        if (!$result instanceof \Magento\Mtf\Config\Dom) {
            throw new \UnexpectedValueException(
                "Instance of the DOM config merger is expected, got {$class} instead."
            );
        }
        return $result;
    }
}

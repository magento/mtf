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

namespace Mtf\Fixture\InjectableFixture\Replacer;

/**
 * Reader for replacing values.
 */
class Reader {
    /**
     * Converter data to array.
     *
     * @var \Magento\Framework\Config\ConverterInterface
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
        $domDocumentClass = 'Magento\Framework\Config\Dom'
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
     * @throws \Magento\Framework\Exception
     */
    public function read($filePath)
    {
        $content = file_get_contents($filePath);
        if (false === $content) {
            throw new \Magento\Framework\Exception("Can't read file: " . $filePath);
        }

        $document = $this->createDocument($this->domDocumentClass, $content);
        if ($this->schema) {
            $errors = [];
            if (!$document->validate($this->schema, $errors)) {
                $message = "Invalid Document \n";
                throw new \Magento\Framework\Exception($message . implode("\n", $errors));
            }
        }

        return $this->converter->convert($document->getDom());
    }

    /**
     * Return newly created instance of a data document.
     *
     * @param string $class
     * @param string $initialContents
     * @return \Magento\Framework\Config\Dom
     * @throws \UnexpectedValueException
     */
    protected function createDocument($class, $initialContents)
    {
        $result = new $class($initialContents);
        if (!$result instanceof \Magento\Framework\Config\Dom) {
            throw new \UnexpectedValueException(
                "Instance of the DOM config merger is expected, got {$class} instead."
            );
        }
        return $result;
    }
}

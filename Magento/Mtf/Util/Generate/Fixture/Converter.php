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

namespace Mtf\Util\Generate\Fixture;

/**
 * Convert fixtures xml to array.
 */
class Converter implements \Magento\Framework\Config\ConverterInterface
{
    /**
     * Interpreter that aggregates named interpreters and delegates every evaluation to one of them.
     *
     * @var \Magento\Framework\Data\Argument\Interpreter\Composite
     */
    protected $argumentInterpreter;

    /**
     * Converter for repository xml files.
     *
     * @var \Mtf\Repository\Reader\Converter
     */
    protected $repositoryConverter;

    /**
     * @constructor
     * @param \Mtf\Repository\Reader\Converter $repositoryConverter
     * @param \Mtf\ObjectManagerFactory $objectManagerFactory
     */
    public function __construct(
        \Mtf\Repository\Reader\Converter $repositoryConverter,
        \Mtf\ObjectManagerFactory $objectManagerFactory
    ) {
        $this->repositoryConverter = $repositoryConverter;
        $objectManager = $objectManagerFactory->getObjectManager();
        $this->argumentInterpreter = $objectManager->get('Magento\Framework\Data\Argument\InterpreterInterface');
    }

    /**
     * Convert xml to array.
     *
     * @param \DOMDocument $source
     * @return array
     */
    public function convert($source)
    {
        $config = $source->getElementsByTagName('fixture');
        $convertedNode = $this->convertFixture($config->item(0));

        return $convertedNode['value'];
    }

    /**
     * Convert element node to array.
     *
     * @param \DOMElement $element
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function convertElement(\DOMElement $element)
    {
        if ($element->hasChildNodes()) {
            $childNodes = $element->childNodes;
            $data = [];

            if (1 == $childNodes->length && XML_ELEMENT_NODE != $childNodes->item(0)->nodeType) {
                $data = $childNodes->item(0)->nodeValue;
            } else {
                foreach ($childNodes as $node) {
                    if ($node instanceof \DOMElement) {
                        $tag = $node->nodeName;
                        $methodName = explode("_", $tag);
                        $methodName = array_map('ucfirst', $methodName);
                        $methodName = implode('', $methodName);
                        $convertMethod = 'convert' . $methodName;

                        $convertMethod = method_exists($this, $convertMethod) ? $convertMethod : 'convertElement';
                        $convertedNode = $this->$convertMethod($node);

                        $isSingle = $convertedNode['isSingle'];
                        $key = $convertedNode['key'];
                        $value = $convertedNode['value'];

                        if ($key) {
                            $data[$tag][$key] = isset($data[$tag][$key]) ? $data[$tag][$key] : [];
                            $data[$tag][$key] = $isSingle ? $value : array_merge($data[$tag][$key], [$value]);
                        } else {
                            $data[$tag] = isset($data[$tag]) ? $data[$tag] : [];
                            $data[$tag] = $isSingle ? $value : array_merge($data[$tag], [$value]);
                        }
                    }
                }
            }
        } else {
            $data = $element->nodeValue;
        }

        return [
            'isSingle' => true,
            'key' => null,
            'value' => $data
        ];
    }

    /**
     * Convert "rule" node to array.
     *
     * @param \DOMElement $element
     * @return array
     */
    protected function convertFixture(\DOMElement $element)
    {
        $data = $this->convertElement($element);
        $class = $element->getAttribute('class');

        $data['isSingle'] = true;
        $data['key'] = null;
        $data['value']['class'] = $class;
        if (isset($data['value']['fields']['field'])) {
            $data['value']['fields'] = $data['value']['fields']['field'];
        }
        return $data;
    }

    /**
     * Convert "rule" node to array.
     *
     * @param \DOMElement $element
     * @return array
     */
    protected function convertField(\DOMElement $element)
    {
        $data = $this->convertElement($element);
        $name = $element->getAttribute('name');

        $data['isSingle'] = true;
        $data['key'] = $name;
        return $data;
    }

    /**
     * Convert "dataset" node to array.
     *
     * @param \DOMElement $element
     * @return array
     */
    protected function convertDataset(\DOMElement $element)
    {
        $data['value'] = $this->repositoryConverter->convertNode($element);
        $data['isSingle'] = true;
        $data['key'] = null;
        return $data;
    }

    /**
     * Convert "data_config" node to array.
     *
     * @param \DOMElement $element
     * @return array
     */
    protected function convertDataConfig(\DOMElement $element)
    {
        $converted = $this->repositoryConverter->convertNode($element);
        $converted['xsi:type'] = 'array';
        $data['value'] = $this->argumentInterpreter->evaluate($converted);
        $data['isSingle'] = true;
        $data['key'] = null;
        return $data;
    }

    /**
     * Convert "default_value" node to array.
     *
     * @param \DOMElement $element
     * @return array
     */
    protected function convertDefaultValue(\DOMElement $element)
    {
        $defaultValue = $this->repositoryConverter->convertNode($element);
        $data['value'] = $this->argumentInterpreter->evaluate($defaultValue);
        $data['isSingle'] = true;
        $data['key'] = null;
        return $data;
    }
}

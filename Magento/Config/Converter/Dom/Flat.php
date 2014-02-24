<?php
/**
 * Converter that dom to array converting all attributes to general array items.
 * Examlpe:
 * <node attr="val">
 *     <subnode>val2<subnode>
 * </node>
 *
 * is converted to
 *
 * array(
 *     'node' => array(
 *         'attr' => 'wal',
 *         'subnode' => 'val2'
 *     )
 * )
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Config\Converter\Dom;

class Flat implements \Magento\Config\ConverterInterface
{
    /**
     * Node identifier attributes
     *
     * @var array
     */
    protected $_idAttributes;

    /**
     * @param $idAttributes
     */
    public function __construct($idAttributes)
    {
        $this->_idAttributes = $idAttributes;
    }

    /**
     * Convert dom node tree to array
     *
     * @param \DOMNode $source
     * @param string $path
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function convert($source, $path = '')
    {
        $nodeListData = array();

        /** @var $node \DOMNode */
        foreach ($source->childNodes as $node) {
            if ($node->nodeType == XML_ELEMENT_NODE) {
                $nodeData = array();
                /** @var $attribute \DOMNode */
                foreach ($node->attributes as $attribute) {
                    if ($attribute->nodeType == XML_ATTRIBUTE_NODE) {
                        $nodeData[$attribute->nodeName] = $attribute->nodeValue;
                    }
                }
                $fullPath = $path . '/' . $node->nodeName;
                $childrenData = $this->convert($node, $fullPath);

                if (is_array($childrenData)) {
                    $nodeData = array_merge($nodeData, $childrenData);
                    if (!count($nodeData)) {
                        $nodeListData[$node->nodeName] = '';
                    } else if (isset($this->_idAttributes[$fullPath])) {
                        $nodeListData[$node->nodeName][$nodeData[$this->_idAttributes[$fullPath]]] = $nodeData;
                    } else {
                        $nodeListData[$node->nodeName] = $nodeData;
                    }
                } else {
                    if (count($nodeData)) {
                        $nodeData['value'] = $childrenData;
                    } else {
                        $nodeData = $childrenData;
                    }
                    $nodeListData[$node->nodeName] = $nodeData;
                }
            } elseif ($node->nodeType == XML_CDATA_SECTION_NODE
                || ($node->nodeType == XML_TEXT_NODE && trim($node->nodeValue) != '')
            ) {
                return (string) $node->nodeValue;
            }
        }
        return $nodeListData;
    }
}

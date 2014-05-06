<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Block;

use Mtf\Fixture\FixtureInterface;
use Mtf\Client\Element;

/**
 * Class Form
 * Is used to represent any form on the page
 *
 * @package Mtf\Block
 * @api
 */
class Form extends Block
{
    /**
     * Mapping for field locator
     *
     * @var array
     */
    protected $mapping = [];

    /**
     * Array of placeholders applied on selector
     *
     * @var array
     */
    protected $placeholders = [];

    /**
     * Determine whether to use only mapped fields
     *
     * @var bool
     */
    protected $mappingMode = false;

    /**
     * Wrap element to pass into form
     *
     * @var string
     */
    protected $wrapper = '';

    /**
     * Mapper instance
     *
     * @var Mapper
     */
    protected $mapper;

    /**
     * Array with filled fields
     *
     * @var array
     */
    public $setFields = [];

    /**
     * @constructor
     * @param Element $element
     * @param Mapper $mapper
     */
    public function __construct(Element $element, Mapper $mapper)
    {
        $this->mapper = $mapper;
        parent::__construct($element);
    }

    /**
     * Initialize block
     * @return void
     */
    protected function _init()
    {
        $xmlFilePath = $this->getXmlFilePath();
        if (file_exists($xmlFilePath)) {
            $mapping = $this->mapper->read($xmlFilePath);
            $this->wrapper = isset($mapping['wrapper']) ? $mapping['wrapper'] : '';
            $this->mapping = isset($mapping['fields']) ? $mapping['fields'] : [];
            $this->mappingMode = isset($mapping['strict']) ? (bool)$mapping['strict'] : false;
            $this->applyPlaceholders();
        }
    }

    /**
     * @return string
     */
    protected function getXmlFilePath()
    {
        return MTF_TESTS_PATH . str_replace('\\', '/', get_class($this)) . '.xml';
    }

    /**
     * @param string $wrapper
     * @return void
     */
    public function setWrapper($wrapper)
    {
        $this->wrapper = $wrapper;
    }

    /**
     * @param array $mapping
     * @return void
     */
    public function setMapping(array $mapping)
    {
        $this->mapping = array_replace($this->mapping, $mapping);
    }

    /**
     * Apply placeholders to selectors.
     * Placeholder in .xml is specified via '%' sign from both side.
     * @return void
     */
    protected function applyPlaceholders()
    {
        foreach ($this->placeholders as $placeholder => $replacement) {
            $pattern = '%' . $placeholder . '%';
            foreach ($this->mapping as $key => $locator) {
                if (strpos($locator['selector'], $pattern) !== false) {
                    $this->mapping[$key]['selector'] = str_replace($pattern, $replacement, $locator['selector']);
                }
            }
        }
    }

    /**
     * Fixture mapping
     *
     * @param array $fields
     * @return array
     */
    protected function dataMapping(array $fields)
    {
        $mapping = [];
        $data = $this->mappingMode ? $this->mapping : $fields;
        foreach ($data as $key => $value) {
            $mapping[$key]['selector'] = isset($this->mapping[$key]['selector'])
                ? $this->mapping[$key]['selector']
                : (($this->wrapper != '') ? "[name='{$this->wrapper}[{$key}]']" : "[name={$key}]");
            $mapping[$key]['strategy'] = isset($this->mapping[$key]['strategy'])
                ? $this->mapping[$key]['strategy']
                : Element\Locator::SELECTOR_CSS;
            $mapping[$key]['input'] = isset($this->mapping[$key]['input'])
                ? $this->mapping[$key]['input']
                : null;
            $mapping[$key]['class'] = isset($this->mapping[$key]['class'])
                ? $this->mapping[$key]['class']
                : null;
            $mapping[$key]['value'] = $this->mappingMode
                ? (isset($fields[$key]['value']) ? $fields[$key]['value'] : $fields[$key])
                : (isset($value['value']) ? $value['value'] : $value);
        }

        return $mapping;
    }

    /**
     * Get element of particular class if defined in xml configuration or of one of framework classes otherwise
     *
     * @param Element $context
     * @param array $field
     * @return Element
     * @throws \Exception
     */
    protected function getElement($context, $field)
    {
        $elementClass = $field['class'];
        if ($elementClass) {
            $element = $context->find($field['selector'], $field['strategy'], $field['class']);
            if (!$element instanceof Element) {
                throw new \Exception('Wrong Element Class.');
            }
        } else {
            $element = $context->find($field['selector'], $field['strategy'], $field['input']);
        }

        return $element;
    }

    /**
     * Fill specified form data
     *
     * @param array $fields
     * @param Element $element
     * @return void
     */
    protected function _fill(array $fields, Element $element = null)
    {
        $context = ($element === null) ? $this->_rootElement : $element;
        foreach ($fields as $name => $field) {
            $element = $this->getElement($context, $field);
            if ($this->mappingMode || ($element->isVisible() && !$element->isDisabled())) {
                $element->setValue($field['value']);
                $this->setFields[$name] = $field['value'];
            }
        }
    }

    /**
     * Fill the root form
     *
     * @param FixtureInterface $fixture
     * @param Element $element
     * @return $this
     */
    public function fill(FixtureInterface $fixture, Element $element = null)
    {
        $data = $fixture->getData();
        $fields = isset($data['fields']) ? $data['fields'] : $data;
        $mapping = $this->dataMapping($fields);
        $this->_fill($mapping, $element);

        return $this;
    }

    /**
     * Verify specified form data
     *
     * @param array $fields
     * @param Element $element
     * @return bool
     */
    protected function _verify(array $fields, Element $element = null)
    {
        $context = ($element === null) ? $this->_rootElement : $element;
        foreach ($fields as $field) {
            $element = $context->find($field['selector'], $field['strategy'], $field['input']);
            if ($this->mappingMode || $element->isVisible()) {
                $value = $element->getValue();
                if ($field['value'] != $value) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Verify the root form
     *
     * @param FixtureInterface $fixture
     * @param Element $element
     * @return bool
     */
    public function verify(FixtureInterface $fixture, Element $element = null)
    {
        $data = $fixture->getData();
        $fields = isset($data['fields']) ? $data['fields'] : $data;
        $mapping = $this->dataMapping($fields);

        return $this->_verify($mapping, $element);
    }
}

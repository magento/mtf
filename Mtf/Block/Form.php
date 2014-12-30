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

namespace Mtf\Block;

use Mtf\Client\Locator;
use Mtf\Client\BrowserInterface;
use Mtf\Fixture\FixtureInterface;
use Mtf\Fixture\InjectableFixture;
use Mtf\Client\Element\SimpleElement;

/**
 * Class Form
 * Is used to represent any form on the page
 *
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
     * @param SimpleElement $element
     * @param BlockFactory $blockFactory
     * @param Mapper $mapper
     * @param BrowserInterface $browser
     * @param array $config [optional]
     */
    public function __construct(
        SimpleElement $element,
        BlockFactory $blockFactory,
        Mapper $mapper,
        BrowserInterface $browser,
        array $config = []
    ) {
        $this->mapper = $mapper;
        parent::__construct($element, $blockFactory, $browser, $config);
    }

    /**
     * Initialize block
     *
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
     * Get path for form *.xml file with mapping
     *
     * @return string
     */
    protected function getXmlFilePath()
    {
        return MTF_TESTS_PATH . str_replace('\\', '/', get_class($this)) . '.xml';
    }

    /**
     * Set wrapper value to the root form
     *
     * @param string $wrapper
     * @return void
     */
    public function setWrapper($wrapper)
    {
        $this->wrapper = $wrapper;
    }

    /**
     * Set mapping to the root form
     *
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
     *
     * @return void
     */
    protected function applyPlaceholders()
    {
        foreach ($this->placeholders as $placeholder => $replacement) {
            $pattern = '%' . $placeholder . '%';
            foreach ($this->mapping as $key => $locator) {
                if (isset($locator['selector']) && strpos($locator['selector'], $pattern) !== false) {
                    $this->mapping[$key]['selector'] = str_replace($pattern, $replacement, $locator['selector']);
                }
            }
        }
    }

    /**
     * Fixture mapping
     *
     * @param array|null $fields
     * @param string|null $parent
     * @return array
     */
    protected function dataMapping(array $fields = null, $parent = null)
    {
        $mapping = [];
        $mappingFields = ($parent !== null) ? $parent : $this->mapping;
        $data = ($this->mappingMode || null === $fields) ? $mappingFields : $fields;
        foreach ($data as $key => $value) {
            if (isset($value['value'])) {
                $value = $value['value'];
            }
            if (!$this->mappingMode && is_array($value) && null !== $fields
                && isset($mappingFields[$key]['composite'])
            ) {
                $mapping[$key] = $this->dataMapping($value, $mappingFields[$key]);
            } else {
                $mapping[$key]['selector'] = isset($mappingFields[$key]['selector'])
                    ? $mappingFields[$key]['selector']
                    : (($this->wrapper != '') ? "[name='{$this->wrapper}" . "[{$key}]']" : "[name={$key}]");
                $mapping[$key]['strategy'] = isset($mappingFields[$key]['strategy'])
                    ? $mappingFields[$key]['strategy']
                    : Locator::SELECTOR_CSS;
                $mapping[$key]['input'] = isset($mappingFields[$key]['input'])
                    ? $mappingFields[$key]['input']
                    : null;
                $mapping[$key]['class'] = isset($mappingFields[$key]['class'])
                    ? $mappingFields[$key]['class']
                    : null;
                $mapping[$key]['value'] = $this->mappingMode
                    ? (isset($fields[$key]['value']) ? $fields[$key]['value'] : $fields[$key])
                    : $value;
            }
        }

        return $mapping;
    }

    /**
     * Get element of particular class if defined in xml configuration or of one of framework classes otherwise
     *
     * @param SimpleElement $context
     * @param array $field
     * @return SimpleElement
     * @throws \Exception
     */
    protected function getElement(SimpleElement $context, array $field)
    {
        if (isset($field['class'])) {
            $element = $context->find($field['selector'], $field['strategy'], $field['class']);
            if (!$element instanceof SimpleElement) {
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
     * @param SimpleElement $element
     * @return void
     */
    protected function _fill(array $fields, SimpleElement $element = null)
    {
        $context = ($element === null) ? $this->_rootElement : $element;
        foreach ($fields as $name => $field) {
            if (!isset($field['value'])) {
                $this->_fill($field, $context);
            } else {
                $element = $this->getElement($context, $field);
                if ($this->mappingMode || ($element->isVisible() && !$element->isDisabled())) {
                    $element->setValue($field['value']);
                    $this->setFields[$name] = $field['value'];
                }
            }
        }
    }

    /**
     * Fill the root form
     *
     * @param FixtureInterface $fixture
     * @param SimpleElement|null $element
     * @return $this
     */
    public function fill(FixtureInterface $fixture, SimpleElement $element = null)
    {
        $data = $fixture->getData();
        $fields = isset($data['fields']) ? $data['fields'] : $data;
        $mapping = $this->dataMapping($fields);
        $this->_fill($mapping, $element);

        return $this;
    }

    /**
     * Get data of specified form data
     *
     * @param array $fields
     * @param SimpleElement|null $element
     * @return array
     */
    protected function _getData(array $fields, SimpleElement $element = null)
    {
        $data = [];
        $context = ($element === null) ? $this->_rootElement : $element;
        foreach ($fields as $key => $field) {
            if (!isset($field['value'])) {
                $data[$key] = $this->_getData($field);
            } else {
                $element = $this->getElement($context, $field);
                if ($this->mappingMode || $element->isVisible()) {
                    $data[$key] = $element->getValue();
                }
            }
        }

        return $data;
    }

    /**
     * Get data of the root form
     *
     * @param FixtureInterface|null $fixture
     * @param SimpleElement|null $element
     * @return array
     */
    public function getData(FixtureInterface $fixture = null, SimpleElement $element = null)
    {
        if (null === $fixture) {
            $fields = null;
        } else {
            $isHasData = ($fixture instanceof InjectableFixture) ? $fixture->hasData() : true;
            $data = $isHasData ? $fixture->getData() : [];
            $fields = isset($data['fields']) ? $data['fields'] : $data;
        }
        $mapping = $this->dataMapping($fields);

        return $this->_getData($mapping, $element);
    }
}

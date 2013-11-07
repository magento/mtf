<?php
/**
 * {license_notice}
 *
 * @api
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Block;

use Mtf\Fixture;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Class Form
 * Is used to represent any form on the page
 *
 * @package Mtf\Block
 */
class Form extends Block
{
    /**
     * Mapping for field locator
     *
     * @var array
     */
    protected $_mapping;

    /**
     * Fixture mapping
     *
     * @param array $fields
     * @return array
     */
    protected function dataMapping(array $fields)
    {
        $mapping = array();
        foreach ($fields as $key => $field) {
            if (isset($field['value'])) {
                $mapping[$key]['selector'] = isset($this->_mapping[$key])
                    ? (isset($this->_mapping[$key]['selector']) ? $this->_mapping[$key]['selector']
                        : $this->_mapping[$key])
                    : (isset($field['selector']) ? $field['selector'] : '#' . $key);

                $mapping[$key]['strategy'] = isset($this->_mapping[$key]['strategy'])
                    ? $this->_mapping[$key]['selector']
                    : (isset($field['selector']) ? $field['selector'] : Locator::SELECTOR_CSS);

                $mapping[$key]['input'] = isset($this->_mapping[$key]['input'])
                    ? $this->_mapping[$key]['input']
                    : (isset($field['input']) ? $field['input'] : null);

                $mapping[$key]['value'] = $field['value'];
            }
        }

        return $mapping;
    }

    /**
     * Fill specified form data
     *
     * @param array $fields
     * @param Element $element
     */
    protected function _fill(array $fields, Element $element = null)
    {
        $context = ($element === null) ? $this->_rootElement : $element;
        foreach ($fields as $field) {
            if (isset($field['value'])) {
                $context->find($field['selector'], $field['strategy'], $field['input'])->setValue($field['value']);
            }
        }
    }

    /**
     * Fill the root form
     *
     * @param Fixture $fixture
     * @param Element $element
     */
    public function fill(Fixture $fixture, Element $element = null)
    {
        $data = $fixture->getData('fields');
        $mapping = $this->dataMapping($data);
        $this->_fill($mapping, $element);
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
            if (isset($field['value'])) {
                $value = $context->find($field['selector'], $field['strategy'], $field['input'])->getValue();
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
     * @param Fixture $fixture
     * @param Element $element
     * @return bool
     */
    public function verify(Fixture $fixture, Element $element = null)
    {
        $data = $fixture->getData('fields');
        $mapping = $this->dataMapping($data);
        return $this->_verify($mapping, $element);
    }
}

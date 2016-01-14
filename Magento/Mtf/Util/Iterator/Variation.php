<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Util\Iterator;

use Magento\Mtf\ObjectManager;
use Magento\Mtf\TestCase\Injectable;
use Magento\Mtf\Util\TestClassResolver;
use Magento\Mtf\TestRunner\Rule\RuleFactory;
use Magento\Mtf\TestRunner\Rule\RuleInterface;
use Magento\Mtf\Config\DataInterface;

/**
 * Test Case variations iterator.
 *
 * @api
 */
class Variation extends AbstractIterator
{
    /**
     * Parent Test Case Object.
     *
     * @var Injectable
     */
    private $testCase;

    /**
     * Test class resolver.
     *
     * @var TestClassResolver
     */
    private $resolver;

    /**
     * Filtering rule.
     *
     * @var RuleInterface
     */
    private $rule;

    /**
     * Configuration data.
     *
     * @var DataInterface
     */
    private $configData;

    /**
     * @constructor
     * @param Injectable $testCase
     * @param TestClassResolver $resolver
     * @param RuleFactory $ruleFactory
     * @param DataInterface $configData
     */
    public function __construct(
        Injectable $testCase,
        TestClassResolver $resolver,
        RuleFactory $ruleFactory,
        DataInterface $configData
    ) {
        $this->testCase = $testCase;
        $this->resolver = $resolver;
        $this->rule = $ruleFactory->create('variation');
        $this->configData = $configData;
        $this->data = $this->getTestCaseMethodVariations();
        $this->initFirstElement();
    }

    /**
     * Check if current element is valid.
     *
     * @return boolean
     */
    protected function isValid()
    {
        $cellTag = isset($this->current['tag']) ? $this->current['tag'] : '';

        if (!$this->rule->apply($cellTag)) {
            return false;
        }
        return true;
    }

    /**
     * Return current data row.
     *
     * @return array
     */
    public function current()
    {
        return $this->prepare();
    }

    /**
     * Get Test Case Method Variations.
     *
     * @return array
     */
    private function getTestCaseMethodVariations()
    {
        $variations = $this->configData->get(
            'testCase/' . get_class($this->testCase) . '/variation',
            ['Default' => []]
        );
        foreach ($variations as $key => &$variation) {
            if (isset($variation['replace'])) {
                unset($variations[$variation['replace']]);
            }
            if (isset($variation['data'])) {
                $variation['variation_name'] = $key;
                $variations[$key] = array_replace($variation, $variation['data']);
                unset($variations[$key]['data']);
            }
        }
        return $variations;
    }

    /**
     * Convert source variation format into normal array.
     *
     * @return array
     */
    private function prepare()
    {
        $data = [];
        if ($this->current) {
            foreach ($this->current as $key => $value) {
                if (strpos($key, '/') === false) {
                    if (isset($value['value'])) {
                        $value = $value['value'];
                    }
                    $data[$key] = $value;
                } else {
                    $this->setArrayPathValue($data, $key, $value);
                }
            }
        }
        return $data;
    }

    /**
     * Transform 'a/b/c' key reference to normal array structure.
     *
     * @param array $data
     * @param string $key
     * @param mixed $value
     * @return void
     */
    private function setArrayPathValue(array &$data, $key, $value)
    {
        $keys = explode('/', $key);
        $key = array_shift($keys);
        while ($key !== null) {
            if (!isset($data[$key])) {
                $data[$key] = null;
                $data = & $data[$key];
            } else {
                $data = & $data[$key];
            }
            $key = array_shift($keys);
        }
        $data = $value;
    }
}

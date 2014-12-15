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

namespace Mtf\Util\Iterator;

use Mtf\ObjectManager;
use Mtf\TestCase\Injectable;
use Mtf\Util\TestClassResolver;
use Mtf\TestRunner\Rule\RuleFactory;
use Mtf\TestRunner\Rule\RuleInterface;

/**
 * Test Case variations iterator.
 *
 * @api
 */
class Variation extends AbstractIterator
{
    /**
     * Column Names.
     *
     * @var array
     */
    protected $header = [];

    /**
     * Parent Test Case Object.
     *
     * @var Injectable
     */
    protected $testCase;

    /**
     * Test class resolver.
     *
     * @var TestClassResolver
     */
    protected $resolver;

    /**
     * Filtering rule.
     *
     * @var RuleInterface
     */
    protected $rule;

    /**
     * @constructor
     * @param Injectable $testCase
     * @param TestClassResolver $resolver
     * @param RuleFactory $ruleFactory
     */
    public function __construct(Injectable $testCase, TestClassResolver $resolver, RuleFactory $ruleFactory)
    {
        $this->testCase = $testCase;
        $this->resolver = $resolver;
        $this->rule = $ruleFactory->create('variation');

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
    protected function getTestCaseMethodVariations()
    {
        $data = [];
        $variationFilePath = $this->getTestCaseMethodVariationFilePath();

        if ($variationFilePath && is_readable($variationFilePath)) {
            $data = $this->readCsv($variationFilePath);
        } else {
            $data['Default'] = [];
        }

        return $data;
    }

    /**
     * Return file path of test case method variations.
     *
     * @return string|null
     */
    protected function getTestCaseMethodVariationFilePath()
    {
        $testFilePath = $this->testCase->getFilePath();
        $variationFilePath = null;

        if (!$testFilePath) {
            $testCaseData = $this->resolver->get('TestCase', [get_class($this->testCase)]);
            if (isset($testCaseData[0]['path'])) {
                $testFilePath = $testCaseData[0]['path'];
            }
        }

        if ($testFilePath) {
            $testMethodName = $this->testCase->getName(false);
            $variationFilePath = str_replace('.php', "/{$testMethodName}.csv", $testFilePath);
        }

        return $variationFilePath;
    }

    /**
     * Parse source file, extract column names information and prepare data array.
     *
     * @param string $variationFilePath
     * @return array
     */
    protected function readCsv($variationFilePath)
    {
        $handle = fopen($variationFilePath, 'r');

        $data = $this->header = [];

        while (($line = fgetcsv($handle, 10000, ';', '"', '\\')) !== false) {
            if ($this->header) {
                $data[] = array_combine($this->header, $line);
            } else {
                $this->header = $line;
            }
        }

        return $data;
    }

    /**
     * Convert source variation format into normal array.
     *
     * @return array
     */
    protected function prepare()
    {
        $data = [];
        if ($this->current) {
            foreach ($this->current as $key => $value) {
                if (strpos($key, '/') === false) {
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
    protected function setArrayPathValue(array &$data, $key, $value)
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

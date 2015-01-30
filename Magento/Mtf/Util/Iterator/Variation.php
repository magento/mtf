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
        $cellTag = isset($this->current['tag']['value']) ? $this->current['tag']['value'] : '';

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
        $classPath = explode('\\', get_class($this->testCase));
        $variations = $this->configData->get(
            'testCase/' . end($classPath) . '/variation',
            ['Default' => []]
        );
        if (isset($variations['data'])) {
            $variations = array_replace($this->current, $this->current['data']);
            unset($variations['data']);
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
        $data = $value['value'];
    }
}

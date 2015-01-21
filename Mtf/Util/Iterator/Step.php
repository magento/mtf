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

use Mtf\TestStep\TestStepFactory;

/**
 * Class Step
 * Step iterator
 */
class Step extends AbstractIterator
{
    /**
     * Factory of steps.
     *
     * @var TestStepFactory
     */
    protected $factory;

    /**
     * Holds result of previous executed steps.
     *
     * @var array
     */
    protected $result = [];

    /**
     * Array with local test run arguments.
     *
     * @var array
     */
    protected $localArguments = [];

    /**
     * Current variation data.
     *
     * @var array
     */
    protected $currentVariation = [];

    /**
     * First step.
     *
     * @var string
     */
    protected $firstStep;

    /**
     * Target module
     *
     * @var string
     */
    protected $module;

    /**
     * @constructor
     * @param TestStepFactory $factory
     * @param array $steps
     * @param array $currentVariation
     * @param array $localArguments
     * @param string $module
     */
    public function __construct(
        TestStepFactory $factory,
        array $steps,
        array $currentVariation,
        array $localArguments,
        $module
    ) {
        $this->module = $module;
        $this->data = $steps;
        $this->firstStep = $steps['first'];
        $this->factory = $factory;
        $this->currentVariation = $currentVariation;
        $this->localArguments = $localArguments;
        $this->result = $this->localArguments;
        $this->rewind();
    }

    /**
     * Initialize Data Array.
     *
     * @return void
     */
    public function rewind()
    {
        $this->current = $this->data[$this->firstStep];
        $this->key = $this->firstStep;
    }

    /**
     * Get current element.
     *
     * @return mixed
     */
    public function current()
    {
        return $this->getStepObject($this->current);
    }

    /**
     * Check if current element is valid.
     *
     * @return boolean
     */
    protected function isValid()
    {
        return array_key_exists($this->key, $this->data);
    }

    /**
     * Seek to next valid row.
     * If current step have next key, iterate $this->data, while array cursor != next step.
     *
     * @return void
     */
    public function next()
    {
        if (isset($this->current['next']) && !empty($this->current['next'])) {
            $this->key = $this->current['next'];
            $this->current = $this->data[$this->key];
        } else {
            $this->current = null;
            $this->key = null;
        }
    }

    /**
     * Prepare step for execute.
     *
     * @param array $step
     * @return mixed
     */
    public function getStepObject(array $step)
    {
        $class = isset($step['class'])
            ? $step['class']
            : str_replace('_', '\\', $this->module)
            . '\Test\TestStep'
            . '\\' . ucfirst($this->key) . 'Step';

        $arguments = $this->result;
        if (isset($step['item'])) {
            $stepArguments = $this->resolveArguments($step['item']);
            $arguments = array_merge($stepArguments, $arguments);
        }
        if (isset($this->currentVariation['arguments'])) {
            $arguments = array_merge($this->currentVariation['arguments'], $arguments);
        }

        return $this->factory->create($class, $arguments);
    }

    /**
     * Resolve arguments
     *
     * @param array $arguments
     * @return array
     */
    protected function resolveArguments($arguments)
    {
        $output = [];
        if (isset($arguments['item'])) {
            $arguments = $arguments['item'];
        }
        foreach ($arguments as $key => $item) {
            if (isset($item['name']) && $item['name'] == $key && !isset($item['value'])) {
                $output[$key] = $this->resolveArguments($item);
            } else if (is_array($item)){
                $output[$key] = $item['value'];
            }
        }
        return $output;
    }

    /**
     * Iterate steps and returns result of step execution.
     *
     * @return array
     */
    public function iterate()
    {
        /** @var \Mtf\TestStep\TestStepInterface $step */
        while ($this->isValid()) {
            $step = $this->current();
            $stepResult = $step->run();
            $stepResult = (is_array($stepResult)) ? $stepResult : [];
            $this->result = array_merge($this->result, $stepResult);
            $this->next();
        }

        return $this->result;
    }
}

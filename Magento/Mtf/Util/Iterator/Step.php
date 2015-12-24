<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Util\Iterator;

use Magento\Mtf\TestStep\TestStepFactory;

/**
 * Step iterator.
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
     * Array with steps object.
     *
     * @var array
     */
    protected $steps = [];

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
     * @constructor
     * @param TestStepFactory $factory
     * @param array $steps
     * @param array $currentVariation
     * @param array $localArguments
     */
    public function __construct(
        TestStepFactory $factory,
        array $steps,
        array $currentVariation,
        array $localArguments
    ) {
        $this->data = $steps;
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
        $this->current = reset($this->data);
        $this->key = key($this->data);
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
        $this->current = next($this->data);
        $this->key = key($this->data);
    }

    /**
     * Prepare step for execute.
     *
     * @param array $step
     * @return mixed
     */
    public function getStepObject(array $step)
    {
        if (isset($step['class'])) {
            $class = $step['class'];
        } else {
            $stepName = isset($step['alias']) ? $step['alias'] : $this->key;
            $class = str_replace('_', '\\', $step['module']) . '\Test\TestStep' . '\\' . ucfirst($stepName) . 'Step';
        }
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
     * Resolve arguments.
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
            } elseif (is_array($item)) {
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
        /** @var \Magento\Mtf\TestStep\TestStepInterface $step */
        while ($this->isValid()) {
            $step = $this->current();
            $this->steps[] = $step;
            $stepResult = $step->run();
            $stepResult = (is_array($stepResult)) ? $stepResult : [];
            $this->result = array_merge($this->result, $stepResult);
            $this->next();
        }

        return $this->result;
    }

    /**
     * Returns all steps.
     *
     * @return array
     */
    public function getAllSteps()
    {
        return $this->steps;
    }
}

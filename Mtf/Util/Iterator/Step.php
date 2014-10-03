<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
     * Factory of steps
     *
     * @var TestStepFactory
     */
    protected $factory;

    /**
     * Holds result of previous executed steps
     *
     * @var array
     */
    protected $result = [];

    /**
     * Array with local test run arguments
     *
     * @var array
     */
    protected $localArguments = [];

    /**
     * Current variation data
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
     * Initialize Data Array
     *
     * @return void
     */
    public function rewind()
    {
        $this->current = reset($this->data);
        $this->key = $this->key();
    }

    /**
     * Get current element
     *
     * @return mixed
     */
    public function current()
    {
        return $this->getStepObject($this->current);
    }

    /**
     * Check if current element is valid
     *
     * @return boolean
     */
    protected function isValid()
    {
        return array_key_exists($this->key, $this->data);
    }

    /**
     * Seek to next valid row
     * If current step have next key, iterate $this->data, while array cursor != next step
     *
     * @return void
     */
    public function next()
    {
        if (isset($this->current['next'])) {
            $this->key = $this->current['next'];
            $this->current = $this->data[$this->key];
        } else {
            $this->current = null;
            $this->key = null;
        }
    }

    /**
     * Prepare step for execute
     *
     * @param array $step
     * @return mixed
     */
    public function getStepObject(array $step)
    {
        $class = isset($step['class'])
            ? $step['class']
            : str_replace('_', '\\', $step['module'])
            . '\Test\TestStep'
            . '\\' . ucfirst($this->key) . 'Step';

        $arguments = $this->result;
        if (isset($step['arguments'])) {
            $arguments = array_merge($step['arguments'], $arguments);
        }
        if (isset($this->currentVariation['arguments'])) {
            $arguments = array_merge($this->currentVariation['arguments'], $arguments);
        }

        return $this->factory->create($class, $arguments);
    }

    /**
     * Iterate steps and returns result of step execution
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

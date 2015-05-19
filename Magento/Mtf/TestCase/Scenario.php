<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\TestCase;

/**
 * Class Scenario
 * Base test case class for functional test using scenario.
 *
 * @api
 * @abstract
 */
abstract class Scenario extends Injectable
{
    /**
     * @var \Magento\Mtf\TestCase\Config\Data
     */
    protected $config;

    /**
     * Step iterator.
     *
     * @var \Magento\Mtf\Util\Iterator\Step
     */
    protected $stepIterator;

    /**
     * @constructor
     * @param null $name [optional]
     * @param array $data [optional]
     * @param string $dataName [optional]
     * @param string $path [optional]
     */
    public function __construct($name = null, array $data = [], $dataName = '', $path = '')
    {
        $this->config = $this->getObjectManager()->get('Magento\Mtf\TestCase\Config\Data');
        parent::__construct($name, $data, $dataName, $path);
    }

    /**
     * Executing prepared scenario.
     *
     * @return void
     */
    protected function executeScenario()
    {
        $pathToClass = explode('\\', get_called_class());
        $testCaseName = end($pathToClass);
        $config = $this->config->get('scenario');
        /** @var \Magento\Mtf\Util\SequencesSorter $sorter */
        $sorter = $this->objectManager->create('Magento\Mtf\Util\SequencesSorter');
        $steps = $sorter->sort(
            $config[$testCaseName]['step'],
            $config[$testCaseName]['firstStep']
        );
        $this->stepIterator = $this->objectManager->create(
            'Magento\Mtf\Util\Iterator\Step',
            [
                'steps' => $steps,
                'testCaseName' => $testCaseName,
                'currentVariation' => $this->currentVariation,
                'localArguments' => $this->localArguments
            ]
        );
        $result = $this->stepIterator->iterate();
        $this->localArguments = array_merge($this->localArguments, $result);
    }

    /**
     * Execute test variation with cleaning up data in steps after scenario execution.
     *
     * @param \PHPUnit_Framework_TestResult $result
     * @param array $variation
     * @return void
     */
    protected function executeTestVariation(\PHPUnit_Framework_TestResult $result, array $variation)
    {
        parent::executeTestVariation($result, $variation);

        if ($this->stepIterator) {
            $this->runStepsCleanup();
        }
    }

    /**
     * Run steps cleanup.
     *
     * @return void
     */
    protected function runStepsCleanup()
    {
        $steps = $this->stepIterator->getAllSteps();
        foreach ($steps as $step) {
            if (method_exists($step, 'cleanup')) {
                $step->cleanup();
            }
        }
    }
}

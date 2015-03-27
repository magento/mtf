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
        /** @var \Magento\Mtf\Util\Iterator\Step $stepIterator */
        $stepIterator = $this->objectManager->create(
            'Magento\Mtf\Util\Iterator\Step',
            [
                'steps' => $steps,
                'testCaseName' => $testCaseName,
                'currentVariation' => $this->currentVariation,
                'localArguments' => $this->localArguments
            ]
        );
        $result = $stepIterator->iterate();
        $this->localArguments = array_merge($this->localArguments, $result);
    }
}

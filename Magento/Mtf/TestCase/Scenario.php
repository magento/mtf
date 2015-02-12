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

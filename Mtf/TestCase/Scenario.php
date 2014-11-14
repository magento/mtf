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

namespace Mtf\TestCase;

use Mtf\Config\Reader;

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
     * Configuration reader.
     *
     * @var Reader
     */
    protected $reader;

    /**
     * @constructor
     * @param null $name [optional]
     * @param array $data [optional]
     * @param string $dataName [optional]
     * @param string $path [optional]
     */
    public function __construct($name = null, array $data = [], $dataName = '', $path = '')
    {
        $this->reader = $this->getObjectManager()->get('Mtf\Config\Reader');
        parent::__construct($name, $data, $dataName, $path);
    }

    /**
     * Executing prepared scenario.
     *
     * @param string $testMethodName
     * @return void
     */
    protected function executeScenario($testMethodName = 'test')
    {
        $result = [];
        $pathToClass = explode('\\', get_called_class());
        $testCaseName = end($pathToClass);
        $config = $this->reader->read('etc');

        if (!empty($config['scenarios'][$testCaseName]['methods'][$testMethodName]['steps'])) {
            $steps = $this->prepareSteps($config['scenarios'][$testCaseName]['methods'][$testMethodName]['steps']);
            /** @var \Mtf\Util\Iterator\Step $stepIterator */
            $stepIterator = $this->objectManager->create(
                'Mtf\Util\Iterator\Step',
                [
                    'steps' => $steps,
                    'testCaseName' => $testCaseName,
                    'testMethodName' => $testMethodName,
                    'currentVariation' => $this->currentVariation,
                    'localArguments' => $this->localArguments
                ]
            );
            $result = $stepIterator->iterate();
        }
        $this->localArguments = array_merge($this->localArguments, $result);
    }

    /**
     * Sort steps using 'next' and 'prev' keys.
     *
     * @param array $steps
     * @throws \Exception
     * @return array
     */
    protected function prepareSteps(array $steps)
    {
        if (!isset($steps['first'])) {
            throw new \Exception("First step hadn't being declared.\n");
        }

        $first = $steps['first'];
        $result = $this->prepareStepSequence($steps, $first);
        $result['first'] = $first;
        $steps = array_diff_key($steps, $result);
        $result = empty($steps) ? $result : $this->interposeSteps($steps, $result);

        return $result;
    }

    /**
     * Put steps from other modules into base scenario.
     *
     * @param array $additionalSteps
     * @param array $baseSteps
     * @return array
     */
    protected function interposeSteps(array $additionalSteps, array $baseSteps)
    {
        $stepKey = key($additionalSteps);
        $chunkOfSteps = $this->prepareStepSequence($additionalSteps, $stepKey);
        $firstStepKey = key($chunkOfSteps);
        end($chunkOfSteps);
        $lastStepKey = key($chunkOfSteps);

        if (isset($chunkOfSteps[$firstStepKey]['prev'])) {
            $this->checkStepExistence($baseSteps, $chunkOfSteps[$firstStepKey]['prev']);
            $prevStepKey = $chunkOfSteps[$firstStepKey]['prev'];
            $chunkOfSteps[$lastStepKey]['next'] = !empty($baseSteps[$prevStepKey]['next'])
                ? $baseSteps[$prevStepKey]['next']
                : null;
            $result[$prevStepKey]['next'] = $firstStepKey;
        }
        if (isset($chunkOfSteps[$lastStepKey]['next'])) {
            $this->checkStepExistence($baseSteps, $chunkOfSteps[$lastStepKey]['next']);
            if ($chunkOfSteps[$lastStepKey]['next'] === $baseSteps['first']) {
                $baseSteps['first'] = $firstStepKey;
            } else {
                $prevStep = $this->getStepWithSameNext($baseSteps, $chunkOfSteps[$lastStepKey]['next']);
                if ($prevStep !== null) {
                    $prevStepKey = key($prevStep);
                    $chunkOfSteps[$lastStepKey]['next'] = $baseSteps[$prevStepKey]['next'];
                    $baseSteps[$prevStepKey]['next'] = $firstStepKey;
                }
            }
        }
        $baseSteps = array_merge($baseSteps, $chunkOfSteps);

        $additionalSteps = array_diff_key($additionalSteps, $chunkOfSteps);
        if (empty($additionalSteps)) {
            return $baseSteps;
        }
        return $this->interposeSteps($additionalSteps, $baseSteps);
    }

    /**
     * Prepare steps by existed sequence.
     *
     * @param array $steps
     * @param string $stepKey
     * @param array $result
     * @return array
     */
    protected function prepareStepSequence(array $steps, $stepKey, array $result = [])
    {
        if (isset($steps[$stepKey])) {
            $result[$stepKey] = $steps[$stepKey];
        }
        if (!isset($steps[$stepKey]) || empty($steps[$stepKey]['next'])) {
            return $result;
        }

        return $this->prepareStepSequence($steps, $steps[$stepKey]['next'], $result);
    }

    /**
     * Returns step with same next.
     *
     * @param array $steps
     * @param string $stepKey
     * @return array|null
     */
    protected function getStepWithSameNext(array $steps, $stepKey)
    {
        foreach ($steps as $key => $value) {
            if (isset($value['next']) && $value['next'] === $stepKey) {
                return [$key => $value];
            }
        }

        return null;
    }

    /**
     * Check step existence in steps array.
     *
     * @param array $steps
     * @param string $stepKey
     * @return void
     * @throws \Exception
     */
    protected function checkStepExistence(array $steps, $stepKey)
    {
        if (!isset($steps[$stepKey])) {
            throw new \Exception("Step with such key '" . $stepKey . "' doesn't exists.\n");
        }
    }
}

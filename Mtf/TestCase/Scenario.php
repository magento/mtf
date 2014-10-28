<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
        $result = array_merge(['first' => $first], $result);
        $steps = array_diff_key($steps, $result);
        $result = empty($steps) ?: $this->interposeSteps($steps, $result);

        return $result;
    }

    /**
     * Put steps from other modules into base scenario.
     *
     * @param array $steps
     * @param array $result
     * @return array
     */
    protected function interposeSteps(array $steps, array $result)
    {
        $stepKey = key($steps);
        $temp = $this->prepareStepSequence($steps, $stepKey);
        $firstStepKey = key($temp);
        end($temp);
        $lastStepKey = key($temp);

        if (isset($temp[$firstStepKey]['prev'])) {
            $this->checkStepExistence($result, $temp[$firstStepKey]['prev']);
            $prevStepKey = $temp[$firstStepKey]['prev'];
            $temp[$lastStepKey]['next'] = isset($result[$prevStepKey]['next']) && !empty($result[$prevStepKey]['next'])
                ? $result[$prevStepKey]['next']
                : null;
            $result[$prevStepKey]['next'] = $firstStepKey;
        }
        if (isset($temp[$lastStepKey]['next'])) {
            $this->checkStepExistence($result, $temp[$lastStepKey]['next']);
            if ($temp[$lastStepKey]['next'] === $result['first']) {
                $result['first'] = $firstStepKey;
            } else {
                $prevStep = $this->getStepWithSameNext($result, $temp[$lastStepKey]['next']);
                if ($prevStep !== null) {
                    $prevStepKey = key($prevStep);
                    $temp[$lastStepKey]['next'] = $result[$prevStepKey]['next'];
                    $result[$prevStepKey]['next'] = $firstStepKey;
                }
            }
        }
        $result = array_merge($result, $temp);

        $steps = array_diff_key($steps, $temp);
        if (empty($steps)) {
            return $result;
        }
        return $this->interposeSteps($steps, $result);
    }

    /**
     * Prepare steps by existed sequence.
     *
     * @param array $steps
     * @param $stepKey
     * @param array $result
     * @return array
     */
    protected function prepareStepSequence(array $steps, $stepKey, array $result = [])
    {
        if (isset($steps[$stepKey])) {
            $result[$stepKey] = $steps[$stepKey];
        }
        if (!isset($steps[$stepKey]) || !isset($steps[$stepKey]['next']) || empty($steps[$stepKey]['next'])) {
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

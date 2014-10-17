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
     * @param null $name
     * @param array $data
     * @param string $dataName
     * @param string $path
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
            $steps = $this->sortSteps($config['scenarios'][$testCaseName]['methods'][$testMethodName]['steps']);
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
    protected function sortSteps(array $steps)
    {
        if (!isset($steps['first'])) {
            throw new \Exception("First step hadn't being declared.\n");
        }

        $result = [];
        $first = $steps['first'];
        $steps = array_diff_assoc($steps, ['first' => $first]);

        foreach ($steps as $key => $step) {
            if (isset($step['prev'])) {
                $this->checkStepExistence($steps, $step['prev']);
                $prevStepKey = $step['prev'];
                $step['next'] = isset($steps[$prevStepKey]['next']) && !empty($steps[$prevStepKey]['next'])
                    ? $steps[$prevStepKey]['next']
                    : null;
                $steps[$prevStepKey]['next'] = $key;
                $result[$prevStepKey] = $steps[$prevStepKey];
            }
            if (isset($step['next'])) {
                $this->checkStepExistence($steps, $step['next']);
                if ($step['next'] === $first) {
                    $first = $key;
                } else {
                    $prevStep = $this->getPreviousStep($result, $key);
                    foreach ($result as $k => $value) {
                        if (isset($value['next']) && $value['next'] === $step['next']) {
                            $result[$k]['next'] = $prevStep !== null ? key($prevStep) : $key;
                        }
                    }
                }
            }
            $result[$key] = $step;
        }
        $result['first'] = $first;

        return $result;
    }

    /**
     * Returns previous step from current step.
     *
     * @param array $steps
     * @param string $step
     * @return array|null
     */
    protected function getPreviousStep(array $steps, $step)
    {
        foreach ($steps as $key => $value) {
            if (isset($value['next']) && $value['next'] === $step) {
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

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\TestCase;

/**
 * Class Scenario
 * Base test case class for functional test using scenario
 *
 * @api
 * @abstract
 */
abstract class Scenario extends Injectable
{
    /**
     * Executing prepared scenario
     *
     * @param array $config
     * @param string $testMethodName
     * @return void
     */
    protected function executeScenario(array $config, $testMethodName = 'test')
    {
        $result = [];
        $pathToClass = explode('\\', get_called_class());
        $testCaseName = end($pathToClass);

        if (!empty($config[$testCaseName]['methods'][$testMethodName]['scenario'])) {
            /** @var \Mtf\Util\Iterator\Step $stepIterator */
            $stepIterator = $this->objectManager->create(
                'Mtf\Util\Iterator\Step',
                [
                    'steps' => $config[$testCaseName]['methods'][$testMethodName]['scenario'],
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
}

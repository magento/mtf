<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Util\Iterator;

use Mtf\ObjectManager;
use Mtf\TestRunner\Configuration;

/**
 * Class ApplicationState
 * @package Mtf\Util\Iterator
 * @api
 */
class ApplicationState extends AbstractIterator
{
    /**
     * Object Manager instance
     *
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Test Runner Configuration object
     *
     * @var \Mtf\TestRunner\Configuration
     */
    protected $testRunnerConfig;

    /**
     * @constructor
     * @param ObjectManager $objectManager
     * @param Configuration $testRunnerConfig
     */
    public function __construct(
        ObjectManager $objectManager,
        Configuration $testRunnerConfig
    ) {
        $this->objectManager = $objectManager;
        $this->testRunnerConfig = $testRunnerConfig;

        $this->data = $this->getAppStates();
        $this->initFirstElement();
    }

    /**
     * Get current element
     *
     * @return array
     */
    public function current()
    {
        return $this->current;
    }

    /**
     * Check if current element is valid
     *
     * @return boolean
     */
    protected function isValid()
    {
        return true;
    }

    /**
     * Get Available Application State Objects
     *
     * @return \Mtf\App\State\StateInterface[]
     */
    protected function getAppStates()
    {
        return [
            'State1' => [
                'class' => 'Mtf\App\State\State1',
                'name' => 'Application Configuration Profile 1',
                'arguments' => []
            ],
            'State2' => [
                'class' => 'Mtf\App\State\State2',
                'name' => 'Application Configuration Profile 2',
                'arguments' => []
            ]
        ];
    }
}

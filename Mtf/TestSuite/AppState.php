<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\TestSuite;

use Mtf\App\State\StateFactory;
use Mtf\App\State\StateInterface;
use Mtf\ObjectManager;

/**
 * Class AppState
 * This Test Suite class uses Application State Iterator to repeat "Test Case Suite"
 * as many times as defined in AppState Configuration
 *
 * @package Mtf\TestSuite
 * @api
 */
class AppState extends TestSuite
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var StateFactory
     */
    protected $appStateFactory;

    /**
     * @constructor
     * @param string $theClass
     * @param string $name
     */
    public function __construct($theClass = '', $name = '')
    {
        $this->initObjectManager();
        $this->appStateFactory = $this->objectManager->get('Mtf\App\State\StateFactory');

        /** @var $applicationStateIterator \Mtf\Util\Iterator\ApplicationState */
        $applicationStateIterator = $this->objectManager->create('Mtf\Util\Iterator\ApplicationState');
        while ($applicationStateIterator->valid()) {
            $appState = $applicationStateIterator->current();
            $callback = [$this, 'appStateCallback'];

            /** @var $suite \Mtf\TestSuite\TestCase */
            $suite = $this->objectManager->create('Mtf\TestSuite\TestCase', ['name' => $appState['name']]);
            $suite->setCallback($callback, $appState);

            $this->addTest(
                $suite,
                \PHPUnit_Util_Test::getGroups(
                    get_class($suite),
                    $suite->getName()
                )
            );

            $applicationStateIterator->next();
        }
        parent::__construct('Application State Runner');
    }

    /**
     * @param string $class
     * @param string $name
     * @param array $arguments
     * @return void
     */
    public function appStateCallback($class, $name = '', array $arguments = [])
    {
        /** @var $appState StateInterface */
        $appState = $this->appStateFactory->create($class, $arguments);
        $appState->apply();
        /** @var \Mtf\System\Event\EventManager $eventManager */
        $eventManager = $this->objectManager->get('Mtf\System\Event\EventManager');
        $eventManager->dispatchEvent(['app_state_applied'], [$name]);
    }

    /**
     * Initialize ObjectManager
     * @return void
     */
    protected function initObjectManager()
    {
        $this->objectManager = \Mtf\ObjectManager::getInstance();
    }
}

<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\TestSuite;

use Magento\Mtf\App\State\StateFactory;
use Magento\Mtf\App\State\StateInterface;
use Magento\Mtf\ObjectManager;

/**
 * Class AppState
 * This Test Suite class uses Application State Iterator to repeat "Test Case Suite"
 * as many times as defined in AppState Configuration
 *
 * @api
 */
class AppState extends Injectable
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
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct($theClass = '', $name = '')
    {
        $this->initObjectManager();
        $this->appStateFactory = $this->objectManager->get('Magento\Mtf\App\State\StateFactory');

        /** @var $applicationStateIterator \Magento\Mtf\Util\Iterator\ApplicationState */
        $applicationStateIterator = $this->objectManager->create('Magento\Mtf\Util\Iterator\ApplicationState');
        while ($applicationStateIterator->valid()) {
            $appState = $applicationStateIterator->current();
            $callback = [$this, 'appStateCallback'];

            /** @var $suite \Magento\Mtf\TestSuite\TestCase */
            $suite = $this->objectManager->create('Magento\Mtf\TestSuite\TestCase', ['name' => $appState['name']]);
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
        /** @var \Magento\Mtf\System\Event\EventManager $eventManager */
        $eventManager = $this->objectManager->get('Magento\Mtf\System\Event\EventManager');
        $eventManager->dispatchEvent(['app_state_applied'], [$name]);
    }

    /**
     * Initialize ObjectManager
     * @return void
     */
    protected function initObjectManager()
    {
        $this->objectManager = \Magento\Mtf\ObjectManager::getInstance();
    }
}

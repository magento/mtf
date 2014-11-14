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

namespace Mtf\TestSuite;

use Mtf\App\State\StateFactory;
use Mtf\App\State\StateInterface;
use Mtf\ObjectManager;

/**
 * Class AppState
 * This Test Suite class uses Application State Iterator to repeat "Test Case Suite"
 * as many times as defined in AppState Configuration
 *
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

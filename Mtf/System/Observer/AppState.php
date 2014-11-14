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
namespace Mtf\System\Observer;

use Mtf\System\Event\Event;
use Mtf\System\Event\State as EventState;
use Mtf\System\Event\ObserverInterface;
use Mtf\Client\Driver\Selenium\Browser;

/**
 * Class AppState
 */
class AppState implements ObserverInterface
{
    /**
     * @var \Mtf\Client\Driver\Selenium\Browser
     */
    protected $browser;

    /**
     * @var EventState
     */
    protected $stateObject;

    /**
     * @param Browser $browser
     * @param EventState $state
     */
    public function __construct(Browser $browser, EventState $state)
    {
        $this->browser = $browser;
        $this->stateObject = $state;
    }

    /**
     * @param Event $event
     * @return void
     */
    public function process(Event $event)
    {
        if (isset($event->getSubjects()[0])) {
            $this->stateObject->setAppStateName($event->getSubjects()[0]);
        }
    }
}

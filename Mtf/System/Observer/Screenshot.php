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

use Mtf\System\Logger;
use Mtf\System\Event\Event;
use Mtf\Client\BrowserInterface;
use Mtf\System\Event\State as EventState;

/**
 * Create screnshot Observer
 */
class Screenshot extends AbstractObserver
{
    /**
     * Image extension
     */
    const FILE_EXTENSION = '.png';

    /**
     * @var \Mtf\Client\BrowserInterface
     */
    protected $browser;

    /**
     * @param Logger $logger
     * @param EventState $state
     * @param BrowserInterface $browser
     */
    public function __construct(Logger $logger, EventState $state, BrowserInterface $browser)
    {
        parent::__construct($logger, $state);
        $this->browser = $browser;
    }

    /**
     * @param Event $event
     * @return void
     */
    public function process(Event $event)
    {
        $this->logger->log(
            $this->browser->getScreenshotData(),
            $this->createDestinationDirectory('screenshots') . '/' . $event->getIdentifier() . self::FILE_EXTENSION
        );
    }
}

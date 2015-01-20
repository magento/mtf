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
 * Observer for obtaining html source of the current page
 */
class SourceCode extends AbstractObserver
{
    /**
     * File name of source code
     */
    const FILE_NAME = 'source_code.log';

    /**
     * Browser object
     *
     * @var BrowserInterface
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
     * Collect page source artifact to storage
     *
     * @param Event $event
     * @return void
     */
    public function process(Event $event)
    {
        $directory = $this->createDestinationDirectory('page-source');
        $this->logger->log($this->browser->getHtmlSource(), $directory . '/' . $event->getIdentifier());
    }
}

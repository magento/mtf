<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Mtf\System\Observer;

use Mtf\Client\Driver\Selenium\Browser;
use Mtf\System\Logger;
use Mtf\System\Event\Event;
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
     * @var Browser
     */
    protected $browser;

    public function __construct(Logger $logger, EventState $state, Browser $browser)
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

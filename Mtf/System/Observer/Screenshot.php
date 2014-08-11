<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Mtf\System\Observer;

use Mtf\System\Event\State as EventState;
use Mtf\System\Event\Event;
use Mtf\System\Logger;
use Mtf\Client\Browser;

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
     * @var \Mtf\Client\Browser
     */
    protected $browser;

    /**
     * @param Logger $logger
     * @param EventState $state
     * @param Browser $browser
     */
    public function __construct(Logger $logger, EventState $state, Browser $browser)
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

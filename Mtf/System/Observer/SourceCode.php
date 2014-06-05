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
use Mtf\System\Event\ObserverInterface;
use Mtf\System\Event\Event;
use Mtf\System\Event\State as EventState;

/**
 * Observer for obtaining html source of the current page
 */
class SourceCode implements ObserverInterface
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

    /**
     * Logger object
     *
     * @var Logger
     */
    protected $logger;

    /**
     * State object
     *
     * @var EventState
     */
    protected $state;

    /**
     * Constructor
     *
     * @param Logger $logger
     * @param Browser $browser
     * @param EventState $state
     */
    public function __construct(
        Logger $logger,
        Browser $browser,
        EventState $state
    ) {
        $this->browser = $browser;
        $this->logger = $logger;
        $this->state = $state;
    }

    /**
     * Create destination directory
     *
     * @return string
     */
    protected function createDestinationDirectory()
    {
        $directory = sprintf('%s/%s/%s/page-source',
            strtolower(str_replace('\\', '-', $this->state->getTestSuiteName())),
            strtolower(str_replace('\\', '-', $this->state->getTestClassName())),
            $this->state->getTestMethodName()
        );
        if (!is_dir($this->logger->getLogDirectoryPath() . '/' . $directory)) {
            mkdir($this->logger->getLogDirectoryPath() . '/' . $directory, 0777, true);
        }
        return $directory;
    }

    /**
     * Collect page source artifact to storage
     *
     * @param Event $event
     * @return void
     */
    public function process(Event $event)
    {
        $directory = $this->createDestinationDirectory();
        $this->logger->log($this->browser->getHtmlSource(), $directory . '/' . $event->getIdentifier());
    }
}

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
use Mtf\System\Event\State;
/**
 * Class SourceCode
 */
class SourceCode implements ObserverInterface
{
    /**
     * File name of source code
     */
    const FILE_NAME = 'source_code.log';

    /**
     * @var Browser
     */
    protected $browser;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var State
     */
    protected $state;

    /**
     * @param Logger $logger
     * @param Browser $browser
     * @param State $state
     */
    public function __construct(
        Logger $logger,
        Browser $browser,
        State $state
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
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        return realpath($directory);
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

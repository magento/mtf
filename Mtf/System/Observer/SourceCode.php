<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Mtf\System\Event;

use Mtf\Client\Driver\Selenium\Browser;
use Mtf\System\Event\ObserverInterface;
use Mtf\System\Logger;
use Mtf\System\Event\Event;
use Mtf\System\Event\State;

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
     * Returns path for event artifact
     *
     * @param Event $event
     * @return string
     */
    protected function getArtifactPath(Event $event)
    {
        return sprintf('%s/%s/%s/page-source/%s',
            $this->state->getTestSuiteName(),
            $this->state->getTestClassName(),
            $this->state->getTestMethodName(),
            $event->getIdentifier()
        );
    }

    /**
     * Collect page source artifact to storage
     *
     * @param Event $event
     * @return void
     */
    public function process(Event $event)
    {
        $this->logger->log($this->browser->getHtmlSource(), $this->getArtifactPath($event));
    }
}

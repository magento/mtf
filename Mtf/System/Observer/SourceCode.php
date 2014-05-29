<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Mtf\System\Event;

use Mtf\Client\Driver\Selenium\Browser;

class SourceCode implements \Mtf\System\Event\ObserverInterface
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
     * @var \Mtf\System\LoggerInterface
     */
    protected $logger;

    /**
     * @var string
     */
    protected $filename;

    /**
     * @param \Mtf\System\LoggerInterface $logger
     * @param Browser $browser
     * @param null|string $filename
     */
    public function __construct(\Mtf\System\LoggerInterface $logger, Browser $browser, $filename = null)
    {
        $this->browser = $browser;
        $this->logger = $logger;
        if (!$filename) {
            $filename = static::FILE_NAME;
        }
        $this->filename = $filename;
    }

    /**
     * @param Event $event
     * @return void
     */
    public function process(\Mtf\System\Event\Event $event)
    {
        $source = $this->browser->getHtmlSource();
        $this->logger->log($source . PHP_EOL, $this->filename);
    }
}

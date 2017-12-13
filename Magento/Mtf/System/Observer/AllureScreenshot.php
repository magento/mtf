<?php
/**
 * Copyright © 2017 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\System\Observer;

use Magento\Mtf\System\Logger;
use Magento\Mtf\System\Event\Event;
use Magento\Mtf\Client\BrowserInterface;
use Magento\Mtf\System\Event\State as EventState;

/**
 * AllureScreenshot observer.
 */
class AllureScreenshot extends AbstractAllureObserver
{
    /**
     * Image extension.
     */
    const FILE_EXTENSION = '.png';

    /**
     * Browser instance.
     *
     * @var BrowserInterface
     */
    protected $browser;

    /**
     * @constructor
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
     * Collect screenshot artifact to storage.
     *
     * @param Event $event
     * @return void
     */
    public function process(Event $event)
    {
        $identifier = $event->getFileIdentifier();
        $file = $this->logger->getLogDirectoryPath() . '/' . $identifier . self::FILE_EXTENSION;
        $this->logger->log($this->browser->getScreenshotData(), $identifier . self::FILE_EXTENSION, 0);
        $this->addAttachment(
            $file,
            'screenshots-' . $identifier,
            'image/' . ltrim(self::FILE_EXTENSION, '.')
        );
        unlink($file);
    }
}

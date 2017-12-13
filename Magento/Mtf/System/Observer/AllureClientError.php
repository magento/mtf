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
 * AllureClientError observer.
 */
class AllureClientError extends AbstractAllureObserver
{
    /**
     * @param Logger $logger
     * @param EventState $state
     * @param BrowserInterface $browser
     * @param string $filename
     */
    public function __construct(Logger $logger, EventState $state, BrowserInterface $browser)
    {
        parent::__construct($logger, $state);
        $this->browser = $browser;
    }

    /**
     * Process current event
     *
     * @param Event $event
     * @return void
     */
    public function process(Event $event)
    {
        $content = '';
        try {
            $errors = $this->browser->getJsErrors();
        } catch (\Exception $exception) {
            $content .= "Unable to get Js Errors. Exception: \n" . $exception . "\n";
        }
        if (!empty($errors)) {
            $content .= $this->getMessagePrefix($event) . "\n";
            foreach ($errors as $url => $jsErrors) {
                $content .= $url . "\n";
                foreach ($jsErrors as $error) {
                    $content .= $error . "\n";
                }
            }
            $this->addAttachment(
                $content,
                'client-js-errors-' . $event->getFileIdentifier(),
                'text/plain'
            );
        }
        $this->browser->injectJsErrorCollector();
    }
}

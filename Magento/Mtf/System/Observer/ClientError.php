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
namespace Magento\Mtf\System\Observer;

use Magento\Mtf\System\Logger;
use Magento\Mtf\System\Event\Event;
use Magento\Mtf\Client\BrowserInterface;
use Magento\Mtf\System\Event\State as EventState;

class ClientError extends AbstractObserver
{
    /**
     * Log file name
     */
    const FILE_NAME = 'client_error.log';

    /**
     * Filename of the log file
     *
     * @var string
     */
    protected $filename;

    /**
     * @param Logger $logger
     * @param EventState $state
     * @param BrowserInterface $browser
     * @param string $filename
     */
    public function __construct(Logger $logger, EventState $state, BrowserInterface $browser, $filename = null)
    {
        parent::__construct($logger, $state);
        $this->browser = $browser;
        $this->filename = $filename ?: static::FILE_NAME;
    }

    /**
     * Process current event
     *
     * @param Event $event
     * @return void
     */
    public function process(Event $event)
    {
        try {
            $errors = $this->browser->getJsErrors();
        } catch (\Exception $exception) {
            $this->logger->log("Unable to get Js Errors. Exception: \n" . $exception . "\n", $this->filename);
        }
        if (!empty($errors)) {
            $this->logger->log($this->getMessagePrefix($event) . "\n", $this->filename);
            foreach ($errors as $url => $jsErrors) {
                $this->logger->log($url . "\n", $this->filename);
                foreach ($jsErrors as $error) {
                    $this->logger->log($error . "\n", $this->filename);
                }
            }
        }
        $this->browser->injectJsErrorCollector();
    }
}

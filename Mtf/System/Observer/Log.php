<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Mtf\System\Observer;

class Log implements \Mtf\System\Event\ObserverInterface
{
    /**
     * Log file name
     */
    const FILE_NAME = 'logger.log';

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
     * @param null|string $filename
     */
    public function __construct(\Mtf\System\LoggerInterface $logger, $filename = null)
    {
        $this->logger = $logger;
        if (!$filename) {
            $filename = static::FILE_NAME;
        }
        $this->filename = $filename;
    }

    /**
     * @param \Mtf\System\Event\Event $event
     *
     * @return void
     */
    public function process(\Mtf\System\Event\Event $event)
    {
        foreach ($event->getSubjects() as $message) {
            $this->logger->log($message . PHP_EOL, $this->filename);
        }
    }
}

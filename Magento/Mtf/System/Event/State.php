<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\System\Event;

/**
 * Class for keeping State of the system
 */
class State
{
    /**
     * Name for current Test suite
     *
     * @var string
     */
    private static $testSuiteName;

    /**
     * Name for current Test class
     *
     * @var string
     */
    private static $testClassName;

    /**
     * Name for current Test method
     *
     * @var string
     */
    private static $testMethodName;

    /**
     * Url of current page
     *
     * @var string
     */
    private $pageUrl = 'about:blank';

    /**
     * @var EventManager
     */
    private $eventManager;

    /**
     * @var string
     */
    private $appStateName = 'No AppState Applied';

    /**
     * @var string
     */
    private $stageName = 'Main Test Flow';

    /**
     * @param EventManager $eventManager
     */
    public function __construct(EventManager $eventManager)
    {
        $this->eventManager = $eventManager;
    }

    /**
     * Setter for testSuiteName
     *
     * @param string $testSuiteName
     * @return void
     */
    public static function setTestSuiteName($testSuiteName)
    {
        self::$testSuiteName = $testSuiteName;
    }

    /**
     * Setter for testClassName
     *
     * @param string $testClassName
     * @return void
     */
    public static function setTestClassName($testClassName)
    {
        self::$testClassName = $testClassName;
    }

    /**
     * Setter for testMethodName
     *
     * @param string $testMethodName
     * @return void
     */
    public static function setTestMethodName($testMethodName)
    {
        self::$testMethodName = $testMethodName;
    }

    /**
     * Setter for pageUrl
     *
     * @param string $pageUrl
     * @return void
     */
    public function setPageUrl($pageUrl)
    {
        if ($pageUrl && $this->pageUrl != $pageUrl) {
            $this->eventManager->dispatchEvent(
                ['page_changed'],
                [sprintf('Page changed from url %s to url %s', $this->pageUrl, $pageUrl)]
            );
            $this->pageUrl = $pageUrl;
        }
    }

    /**
     * Getter for $testSuiteName
     *
     * @return string
     */
    public static function getTestSuiteName()
    {
        return self::$testSuiteName ?: 'default';
    }

    /**
     * Getter for $testClassName
     *
     * @return string
     */
    public static function getTestClassName()
    {
        return self::$testClassName ?: 'default';
    }

    /**
     * Getter for $testMethodName
     *
     * @return string
     */
    public static function getTestMethodName()
    {
        return self::$testMethodName ?: 'default';
    }

    /**
     * Getter for current pageUrl
     *
     * @return string
     */
    public function getPageUrl()
    {
        return $this->pageUrl ?: 'default';
    }

    /**
     * Set application state name
     *
     * @param string $appStateName
     * @return void
     */
    public function setAppStateName($appStateName)
    {
        $this->appStateName = $appStateName;
    }

    /**
     * Get application state name
     *
     * @return string
     */
    public function getAppStateName()
    {
        return $this->appStateName;
    }

    /**
     * Set stage name (Currently persisting fixture class name or 'Main Test Flow')
     *
     * @param string $stageName
     * @return void
     */
    public function setStageName($stageName = null)
    {
        $this->stageName = $stageName ?: 'Main Test Flow';
    }

    /**
     * Get current stage name
     *
     * @return string
     */
    public function getStageName()
    {
        return $this->stageName;
    }
}

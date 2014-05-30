<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Mtf\System\Event;

/**
 * Class State
 * @package Mtf\System\Event
 */
class State
{
    /**
     * @var string
     */
    private $testSuiteName;

    /**
     * @var string
     */
    private $testClassName;

    /**
     * @var string
     */
    private $testMethodName;

    /**
     * @var string
     */
    private $handlerName;

    /**
     * @var string
     */
    private $pageUrl;

    /**
     * @param string $testSuiteName
     */
    public function setTestSuiteName($testSuiteName)
    {
        $this->testSuiteName = $testSuiteName;
    }

    /**
     * @param string $testClassName
     */
    public function setTestClassName($testClassName)
    {
        $this->testClassName = $testClassName;
    }

    /**
     * @param string $testMethodName
     */
    public function setTestMethodName($testMethodName)
    {
        $this->testClassName = $testMethodName;
    }

    /**
     * @param string $hendlerName
     */
    public function startHandlerPersist($hendlerName)
    {
        $this->handlerName = $hendlerName;
    }

    /**
     * Clear handler property
     */
    public function stopHandlerPersist()
    {
        $this->handlerName = null;
    }

    /**
     * @param string $pageUrl
     */
    public function setPageUrl($pageUrl)
    {
        $this->pageUrl = $pageUrl;
    }

    /**
     * @return string
     */
    public function getTestSuiteName()
    {
        return $this->testSuiteName;
    }

    /**
     * @return string
     */
    public function getTestClassName()
    {
        return $this->testClassName;
    }

    /**
     * @return string
     */
    public function getTestMethodName()
    {
        return $this->testMethodName;
    }

    /**
     * @return string
     */
    public function getHandlerName()
    {
        return $this->handlerName;
    }

    /**
     * @return string
     */
    public function getPageUrl()
    {
        return $this->pageUrl;
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Mtf\System\Event;

/**
 * Class Event
 * @package Mtf\System\Event
 */
class State
{
    /**
     * Precondition test stage
     */
    const TEST_STAGE_PRECONDITION = 'precondition';

    /**
     * Main test flow stage
     */
    const TEST_STAGE_TEST = 'test';

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
    private $testStage;

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
     * @param string $testStageName
     */
    public function setTestStage($testStageName)
    {
        $this->testStage = $testStageName;
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
    public function getTestStage()
    {
        return $this->testStage;
    }

    /**
     * @return string
     */
    public function getPageUrl()
    {
        return $this->pageUrl;
    }
}

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
     * Test main flow stage
     */
    const TEST_MAIN_FLOW_STAGE = 'Test Main Flow';

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
    private $stageName = self::TEST_MAIN_FLOW_STAGE;

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
     * @param string $fixtureName
     */
    public function startFixturePersist($fixtureName)
    {
        $this->stageName = 'Persisting ' . $fixtureName;
    }

    /**
     * Clear handler property
     */
    public function stopFixturePersist()
    {
        $this->stageName = self::TEST_MAIN_FLOW_STAGE;
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
    public function getStageName()
    {
        return $this->stageName;
    }

    /**
     * @return string
     */
    public function getPageUrl()
    {
        return $this->pageUrl;
    }
}

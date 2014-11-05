<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Util\Iterator;

use Mtf\Util\TestClassModuleFilter;
use Mtf\TestRunner\Rule\SuiteRuleInterface;
use Mtf\TestRunner\Configuration;
use Mtf\Util\TestClassResolver;
use Mtf\ObjectManager;

/**
 * Class TestCase
 *
 * @api
 */
class TestCase extends AbstractIterator
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var Configuration
     */
    protected $testRunnerConfig;

    /**
     * @var TestClassResolver
     */
    protected $testClassResolver;

    /**
     * @var SuiteRuleInterface
     */
    protected $rule;

    /**
     * @var TestClassModuleFilter
     */
    protected $moduleFilter;

    /**
     * @constructor
     * @param ObjectManager $objectManager
     * @param Configuration $testRunnerConfig
     * @param TestClassResolver $testClassResolver
     * @param TestClassModuleFilter $moduleFilter
     * @param SuiteRuleInterface $rule
     */
    public function __construct(
        ObjectManager $objectManager,
        Configuration $testRunnerConfig,
        TestClassResolver $testClassResolver,
        TestClassModuleFilter $moduleFilter,
        SuiteRuleInterface $rule
    ) {
        $this->objectManager = $objectManager;
        $this->testRunnerConfig = $testRunnerConfig;
        $this->testClassResolver = $testClassResolver;
        $this->moduleFilter = $moduleFilter;
        $this->rule = $rule;

        $this->data = $this->collectTestCases();
        $this->data = $this->moduleFilter->applyFilter($this->data);
        $this->initFirstElement();
    }

    /**
     * Get current element
     *
     * @return mixed
     */
    public function current()
    {
        return $this->current;
    }

    /**
     * Check if current element is valid
     *
     * @return boolean
     */
    protected function isValid()
    {
        $class = $this->current['class'];
        if (!$this->rule->apply($class)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get list of available Test Case classes (without filtering)
     * Available keys:
     *  - class
     *  - name
     *  - path
     *
     * @return array
     */
    protected function collectTestCases()
    {
        return $this->testClassResolver->get('TestCase');
    }
}

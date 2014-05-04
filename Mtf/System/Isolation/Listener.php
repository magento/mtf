<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\System\Isolation;

use Mtf\System\Isolation\Driver;
use Mtf\System\Config;

/**
 * Class Listener
 *
 * Listener which provides framework with isolation capability
 *
 * @package Mtf\System\Isolation
 * @internal
 */
class Listener implements \PHPUnit_Framework_TestListener
{
    /**
     * Isolation mode
     */
    const MODE_NONE = 'none';
    const MODE_BEFORE = 'before';
    const MODE_AFTER = 'after';
    const MODE_BOTH = 'both';

    /**
     * Isolation scope
     */
    const SCOPE_TEST = 'test';
    const SCOPE_TEST_CASE = 'testCase';
    const SCOPE_TEST_SUITE = 'testSuite';

    /**
     * Is isolation done
     *
     * @var bool
     */
    private $_isolated = false;

    /**
     * Is isolation required before next scope execution
     *
     * @var bool
     */
    private $_isolationRequired = false;

    /**
     * Stack of scopes
     *
     * @var array
     */
    private $_modeStack = [];

    /**
     * Default stack of scopes
     *
     * @var array
     */
    private $_defaultModeStack = [];

    /**
     * Last default modes
     *
     * @var array
     */
    private $_lastDefaultModes = [];

    /**
     * Isolation driver instance
     *
     * @var \Mtf\System\Isolation\Driver
     */
    private $_driver;

    /**
     * @param \Mtf\System\Isolation\Driver $driver
     * @param null|\Mtf\System\Config $configuration
     */
    public function __construct(Driver $driver, $configuration = null)
    {
        if (!isset($configuration)) {
            $configuration = new Config();
        }
        $this->_driver = $driver;
        $this->_lastDefaultModes = [
            self::SCOPE_TEST_SUITE => $configuration->getConfigParam('isolation/' . self::SCOPE_TEST_SUITE),
            self::SCOPE_TEST_CASE => $configuration->getConfigParam('isolation/' . self::SCOPE_TEST_CASE),
            self::SCOPE_TEST => $configuration->getConfigParam('isolation/' . self::SCOPE_TEST),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function startTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        $scope = $this->_getSuiteScope($suite);
        if (!$scope) {
            return;
        }
        $className = $scope == self::SCOPE_TEST_CASE ? $suite->getName() : get_class($suite);
        $annotations = \PHPUnit_Util_Test::parseTestMethodAnnotations($className);
        $this->_processBeforeScope($annotations['class'], $scope);
    }

    /**
     * {@inheritdoc}
     */
    public function endTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        $this->_isolated = false;
        $scope = $this->_getSuiteScope($suite);
        if (!$scope) {
            return;
        }
        $this->_processAfterScope();
    }

    /**
     * {@inheritdoc}
     */
    public function startTest(\PHPUnit_Framework_Test $test)
    {
        $annotations = \PHPUnit_Util_Test::parseTestMethodAnnotations(get_class($test), $test->getName());
        $this->_processBeforeScope($annotations['method'], self::SCOPE_TEST);
    }

    /**
     * {@inheritdoc}
     */
    public function endTest(\PHPUnit_Framework_Test $test, $time)
    {
        $this->_isolated = false;
        $this->_processAfterScope();
    }

    /**
     * Get isolation mode from annotation
     *
     * @param array $annotation
     * @param string $scope
     * @return string
     */
    private function _getMode(array $annotation, $scope)
    {
        if (!isset($annotation['isolation'])) {
            return $this->_lastDefaultModes[$scope];
        }
        $newDefaultModes = $this->_lastDefaultModes;
        $mode = false;
        $available = [self::MODE_BOTH, self::MODE_AFTER, self::MODE_BEFORE, self::MODE_NONE];
        foreach ($annotation['isolation'] as $isolationMode) {
            $isolationMode = trim($isolationMode);
            $scopeDefaultMode = false;
            foreach (array_keys($newDefaultModes) as $defaultModeScope) {
                if (strpos($isolationMode, $defaultModeScope) !== 0) {
                    continue;
                }
                $newDefaultMode = trim(str_replace($defaultModeScope, '', $isolationMode));
                if (in_array($newDefaultMode, $available)) {
                    $newDefaultModes[$defaultModeScope] = $newDefaultMode;
                    $scopeDefaultMode = true;
                }
            }
            if (!$scopeDefaultMode && in_array($isolationMode, $available)) {
                $mode = $isolationMode;
            }
        }
        $mode = $mode ? : $this->_lastDefaultModes[$scope];
        $this->_lastDefaultModes = $newDefaultModes;
        return $mode;
    }

    /**
     * Get suite scope
     *
     * @param \PHPUnit_Framework_TestSuite $suite
     * @return bool|string
     */
    private function _getSuiteScope(\PHPUnit_Framework_TestSuite $suite)
    {
        if (class_exists($suite->getName()) && is_subclass_of($suite->getName(), '\\PHPUnit_Framework_TestCase')) {
            return self::SCOPE_TEST_CASE;
        } elseif (is_subclass_of($suite, '\\PHPUnit_Framework_TestSuite')) {
            return self::SCOPE_TEST_SUITE;
        }
        return false;
    }

    /**
     * Do isolation checks before scope
     *
     * @param array $annotation
     * @param string $scope
     * @return void
     */
    private function _processBeforeScope(array $annotation, $scope)
    {
        $mode = $this->_getMode($annotation, $scope);
        array_push($this->_defaultModeStack, $this->_lastDefaultModes);
        if (!$this->_isolated
            && ($this->_isolationRequired || in_array($mode, [self::MODE_BOTH, self::MODE_BEFORE]))
        ) {
            $this->_driver->isolate();
            $this->_isolated = true;
            $this->_isolationRequired = false;
        }
        array_push($this->_modeStack, $mode);
    }

    /**
     * Do isolation checks after scope
     * @return void
     */
    private function _processAfterScope()
    {
        $mode = array_pop($this->_modeStack);
        $this->_lastDefaultModes = array_pop($this->_defaultModeStack);
        if (in_array($mode, [self::MODE_BOTH, self::MODE_AFTER])) {
            $this->_isolationRequired = true;
        }
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addError(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addFailure(\PHPUnit_Framework_Test $test, \PHPUnit_Framework_AssertionFailedError $e, $time)
    {
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addIncompleteTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addRiskyTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addSkippedTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
    }
}

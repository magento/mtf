<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\TestCase;

use Mtf\Constraint\AbstractConstraint;
use Mtf\TestRunner\Process\ProcessManager;

/**
 * Class Injectable
 *
 * Class is extended from Functional
 * and is base test case class for functional testing
 *
 * @package Mtf\TestCase
 * @api
 * @abstract
 */
abstract class Injectable extends Functional
{
    /**
     * Test case full name
     *
     * @var string
     */
    protected $dataId;

    /**
     * Variation identifier
     *
     * @var string
     */
    protected $variationName;

    /**
     * Test case file path
     *
     * @var string
     */
    protected $filePath;

    /**
     * Abstract Constraint instance
     *
     * @var AbstractConstraint
     */
    protected $constraint;

    /**
     * Array with shared arguments between variations for non-sharable objects
     *
     * @var array
     */
    protected static $sharedArguments = [];

    /**
     * Constructs a test case with the given name
     *
     * @constructor
     * @param null $name
     * @param array $data
     * @param string $dataName
     * @param string $path
     */
    public function __construct($name = null, array $data = [], $dataName = '', $path = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->dataId = get_class($this) . '::' . $name;
        if (!isset(static::$sharedArguments[$this->dataId]) && method_exists($this, '__prepare')) {
            static::$sharedArguments[$this->dataId] = (array) $this->objectManager->invoke($this, '__prepare');
        }
        if (method_exists($this, '__inject')) {
            $this->objectManager->invoke(
                $this, '__inject',
                isset(self::$sharedArguments[$this->dataId]) ? self::$sharedArguments[$this->dataId] : []
            );
        }
        $this->filePath = $path;
    }

    /**
     * Get file path
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * Set variation name
     *
     * @param string $variationName
     * @return void
     */
    public function setVariationName($variationName)
    {
        $this->variationName = $variationName;
    }

    /**
     * Run with Variations Iterator
     *
     * @param \PHPUnit_Framework_TestResult $result
     * @return \PHPUnit_Framework_TestResult
     */
    public function run(\PHPUnit_Framework_TestResult $result = NULL)
    {
        /** @var $testVariationIterator \Mtf\Util\Iterator\TestCaseVariation */
        $testVariationIterator = $this->objectManager->create('Mtf\Util\Iterator\TestCaseVariation',
            [
                'testCase' => $this
            ]
        );
        while ($testVariationIterator->valid()) {
            if ($this->isParallelRun) {
                // Running this test instance in a new thread.  Arguments will be merged when test runs on new thread.
                $this->setVariationName($testVariationIterator->key());
                parent::run($result);
            } else {
                $variation = $testVariationIterator->current();
                $this->executeTestVariation($result, $variation);
            }
            $testVariationIterator->next();
        }
        self::$sharedArguments = [];

        return $result;
    }

    /**
     * Execute test variation
     *
     * @param \PHPUnit_Framework_TestResult $result
     * @param array $variation
     * @return void
     */
    protected function executeTestVariation(\PHPUnit_Framework_TestResult $result, array $variation)
    {
        // remove constraint object from previous test case variation iteration
        $this->constraint = null;

        $variationName = $variation['id'];
        $this->setVariationName($variationName);

        $arguments = isset($variation['arguments'])
            ? array_merge(
                $variation['arguments'],
                isset(self::$sharedArguments[$this->dataId]) ? self::$sharedArguments[$this->dataId] : []
            ) : [];
        $this->setDependencyInput($arguments);

        if (isset($variation['constraint'])) {
            $this->constraint = $variation['constraint'];
            $this->constraint->configure($this, $arguments);
        }
        parent::run($result);
    }

    /**
     * Override to run attached constraint if available
     *
     * @return mixed
     * @throws \PHPUnit_Framework_Exception
     */
    protected function runTest()
    {
        $testResult = parent::runTest();
        if ($this->constraint) {
            self::assertThat($this->getName(), $this->constraint);
        }
        return $testResult;
    }

    /**
     * Gets the data set description of a TestCase.
     *
     * @param  boolean $includeData
     * @return string
     */
    protected function getDataSetAsString($includeData = true)
    {
        $buffer = '';

        if (isset($this->variationName)) {
            if (!empty($this->variationName)) {
                if (is_int($this->variationName)) {
                    $buffer .= sprintf(' with data set #%d', $this->variationName);
                } else {
                    $buffer .= sprintf(' with data set "%s"', $this->variationName);
                }
            }
            if ($includeData) {
                $buffer .= sprintf(' (%s)', $this->dataToString($this->variationName));
            }
        } else {
            $buffer = parent::getDataSetAsString($includeData);
        }

        return $buffer;
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\TestCase;

use Mtf\Constraint\AbstractConstraint;

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
     * Array with local test run arguments
     *
     * @var array
     */
    protected $localArguments = [];

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
    public function run(\PHPUnit_Framework_TestResult $result = null)
    {
        if ($this->isParallelRun) {
            parent::run($result);
        }
        if (!isset(static::$sharedArguments[$this->dataId]) && method_exists($this, '__prepare')) {
            static::$sharedArguments[$this->dataId] = (array)$this->objectManager->invoke($this, '__prepare');
        }
        /** @var $testVariationIterator \Mtf\Util\Iterator\TestCaseVariation */
        $testVariationIterator = $this->objectManager->create(
            'Mtf\Util\Iterator\TestCaseVariation',
            [
                'testCase' => $this
            ]
        );
        while ($testVariationIterator->valid()) {
            if (method_exists($this, '__inject')) {
                $this->localArguments = $this->objectManager->invoke(
                    $this,
                    '__inject',
                    isset(self::$sharedArguments[$this->dataId]) ? self::$sharedArguments[$this->dataId] : []
                );
                if (!$this->localArguments || !is_array($this->localArguments)) {
                    $this->localArguments = [];
                }
            }
            if (isset(static::$sharedArguments[$this->dataId])) {
                $this->localArguments = array_merge(static::$sharedArguments[$this->dataId], $this->localArguments);
            }
            $variation = $this->prepareVariation(
                $testVariationIterator->current(),
                $this->localArguments
            );
            $this->executeTestVariation($result, $variation);
            $testVariationIterator->next();
            $this->localArguments = [];
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
            ? $variation['arguments']
            : [];
        $this->setDependencyInput($arguments);

        if (isset($variation['constraint'])) {
            $this->constraint = $variation['constraint'];
            $this->localArguments = array_merge($arguments, $this->localArguments);
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
        $this->localArguments = array_merge($this->localArguments, is_array($testResult) ? $testResult : []);
        if ($this->constraint) {
            $this->constraint->configure($this, $this->localArguments);
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
                $buffer .= sprintf(' (%s)', $this->variationName);
            }
        } else {
            $buffer = parent::getDataSetAsString($includeData);
        }

        return $buffer;
    }

    /**
     * Prepare variation for Test Case Method
     *
     * @param array $variation
     * @param array $arguments
     * @return array
     */
    protected function prepareVariation(array $variation, array $arguments)
    {
        if (isset($variation['arguments'])) {
            $arguments = array_merge($variation['arguments'], $arguments);
        }
        $resolvedArguments = $this->objectManager
            ->prepareArguments($this, $this->getName(false), $arguments);

        if (isset($arguments['constraint'])) {
            $parameters = $this->objectManager->getParameters($this, $this->getName(false));
            if (isset($parameters['constraint'])) {
                $resolvedArguments['constraint'] = $this->prepareConstraintObject($arguments['constraint']);
            } else {
                $variation['constraint'] = $this->prepareConstraintObject($arguments['constraint']);
            }
        }

        $variation['arguments'] = $resolvedArguments;

        return $variation;
    }

    /**
     * Prepare configuration object
     *
     * @param string $constraints
     * @return \Mtf\Constraint\Composite
     */
    protected function prepareConstraintObject($constraints)
    {
        $constraintsArray = array_map('trim', explode(',', $constraints));
        return $this->objectManager->create('Mtf\Constraint\Composite', ['constraints' => $constraintsArray]);
    }
}

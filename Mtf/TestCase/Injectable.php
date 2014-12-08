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

namespace Mtf\TestCase;

use Mtf\Constraint\AbstractConstraint;

/**
 * Class Injectable
 *
 * Class is extended from Functional
 * and is base test case class for functional testing
 *
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
     * Current variation data
     *
     * @var array
     */
    protected $currentVariation = [];

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
            return parent::run($result);
        }
        try {
            if (!isset(static::$sharedArguments[$this->dataId]) && method_exists($this, '__prepare')) {
                static::$sharedArguments[$this->dataId] = (array) $this->getObjectManager()->invoke($this, '__prepare');
            }
            /** @var $testVariationIterator \Mtf\Util\Iterator\TestCaseVariation */
            $testVariationIterator = $this->getObjectManager()->create(
                'Mtf\Util\Iterator\TestCaseVariation',
                ['testCase' => $this]
            );
            while ($testVariationIterator->valid()) {
                if (method_exists($this, '__inject')) {
                    $this->localArguments = $this->getObjectManager()->invoke(
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
                $this->currentVariation = $testVariationIterator->current();
                $variation = $this->prepareVariation(
                    $this->currentVariation,
                    $this->localArguments
                );
                $this->executeTestVariation($result, $variation);
                $testVariationIterator->next();
                $this->localArguments = [];
            }
        } catch (\PHPUnit_Framework_Exception $phpUnitException) {
            throw $phpUnitException;
        } catch (\Exception $exception) {
            $this->fail($exception);
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
        if (isset($this->currentVariation['arguments']['issue'])
            && !empty($this->currentVariation['arguments']['issue'])
        ) {
            $this->markTestIncomplete($this->currentVariation['arguments']['issue']);
        }
        $testResult = parent::runTest();
        $this->localArguments = array_merge($this->localArguments, is_array($testResult) ? $testResult : []);
        $arguments = array_merge($this->currentVariation['arguments'], $this->localArguments);
        if ($this->constraint) {
            $this->constraint->configure($arguments);
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
        $resolvedArguments = $this->getObjectManager()
            ->prepareArguments($this, $this->getName(false), $arguments);

        if (isset($arguments['constraint'])) {
            $parameters = $this->getObjectManager()->getParameters($this, $this->getName(false));
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
        return $this->getObjectManager()->create('Mtf\Constraint\Composite', ['codeConstraints' => $constraintsArray]);
    }
}

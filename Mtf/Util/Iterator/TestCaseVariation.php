<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Util\Iterator;

use Mtf\ObjectManager;
use Mtf\TestCase\Injectable;
use Mtf\TestRunner\Configuration;

/**
 * Class TestCaseVariation
 *
 * @package Mtf\Util\Iterator
 * @api
 */
class TestCaseVariation extends AbstractIterator
{
    /**
     * @var Injectable
     */
    protected $testCase;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var Configuration
     */
    protected $testRunnerConfig;

    /**
     * Tmp counter
     *
     * @var int
     */
    protected static $count = 0;

    /**
     * @constructor
     * @param ObjectManager $objectManager
     * @param Injectable $testCase
     * @param Configuration $testRunnerConfig
     */
    public function __construct(
        ObjectManager $objectManager,
        Injectable $testCase,
        Configuration $testRunnerConfig
    ) {
        $this->objectManager = $objectManager;
        $this->testCase = $testCase;
        $this->testRunnerConfig = $testRunnerConfig;

        $this->data = $this->getVariations();
        $this->initFirstElement();
    }

    /**
     * Get current element
     *
     * @return mixed
     */
    public function current()
    {
        return $this->prepare();
    }

    /**
     * Check if current element is valid
     *
     * @return boolean
     */
    protected function isValid()
    {
        return true;
    }

    /**
     * Get list of variations table
     *
     * @return array
     */
    protected function getVariations()
    {
        self::$count = 0;
        $variations = [];

        /** @var $source \Mtf\Util\Iterator\Variation */
        $source = $this->objectManager->create('Mtf\Util\Iterator\Variation', ['testCase' => $this->testCase]);
        if (count($source)) {
            while ($variation = $source->current()) {
                if (!$source->valid()) {
                    break;
                }
                ++self::$count;
                $variations[self::$count] = $variation;
                $source->next();
            }
        } else {
            $variations['Default'] = [];
        }

        return $variations;
    }

    /**
     * Prepare variation for Test Case Method
     *
     * @return array
     */
    protected function prepare()
    {
        $variation = [
            'id' => $this->key()
        ];

        $resolvedArguments = $this->objectManager
            ->prepareArguments($this->testCase, $this->testCase->getName(false), $this->current);

        if (isset($this->current['constraint'])) {
            $parameters = $this->objectManager->getParameters($this->testCase, $this->testCase->getName(false));
            if (isset($parameters['constraint'])) {
                $resolvedArguments['constraint'] = $this->prepareConstraintObject($this->current['constraint']);
            } else {
                $variation['constraint'] = $this->prepareConstraintObject($this->current['constraint']);
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

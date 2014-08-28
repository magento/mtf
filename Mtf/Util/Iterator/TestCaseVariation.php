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
            'id' => $this->key(),
            'arguments' => $this->current
        ];
        return $variation;
    }
}

<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Util\Iterator;

use Magento\Mtf\ObjectManager;
use Magento\Mtf\TestCase\Injectable;
use Magento\Mtf\Config\DataInterface;

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
     * @var DataInterface
     */
    protected $configData;

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
     * @param DataInterface $configData
     */
    public function __construct(
        ObjectManager $objectManager,
        Injectable $testCase,
        DataInterface $configData
    ) {
        $this->objectManager = $objectManager;
        $this->testCase = $testCase;
        $this->configData = $configData;

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

        /** @var $source \Magento\Mtf\Util\Iterator\Variation */
        $source = $this->objectManager->create('Magento\Mtf\Util\Iterator\Variation', ['testCase' => $this->testCase]);
        if (count($source)) {
            while ($variation = $source->current()) {
                if (!$source->valid()) {
                    break;
                }
                ++self::$count;
                $variations[self::$count] = $variation;
                $source->next();
            }
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

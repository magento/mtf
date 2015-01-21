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

namespace Mtf\Util\Iterator;

use Mtf\ObjectManager;
use Mtf\TestCase\Injectable;
use Mtf\Config\DataInterface;

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

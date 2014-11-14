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

namespace Mtf\TestRunner\Rule;

use Mtf\TestRunner\Configuration;
use Mtf\ObjectManager;

/**
 * Class AbstractRule
 *
 * @api
 */
abstract class AbstractRule
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
     * @constructor
     * @param ObjectManager $objectManager
     * @param Configuration $testRunnerConfig
     */
    public function __construct(
        ObjectManager $objectManager,
        Configuration $testRunnerConfig
    ) {
        $this->objectManager = $objectManager;
        $this->testRunnerConfig = $testRunnerConfig;
    }

    /**
     * Process all filter
     *
     * @param array $rule
     * @return bool
     */
    protected function processRule(array $rule)
    {
        // @todo implement as standalone rule classes
        $result = true;
        if (isset($rule['allow'])) {
            $result = $this->processAllowFilters($rule['allow']);
        }
        if ($result === true) {
            if (isset($rule['deny'])) {
                $result = $this->processDenyFilters($rule['deny']);
            }
        }
        return $result;
    }

    /**
     * Process `allow` filters
     *
     * @param array $filters
     * @return bool
     */
    protected function processAllowFilters(array $filters)
    {
        $result = true;
        foreach ($filters as $key => $value) {
            $matchMethodName = $key . 'Match';
            if (method_exists($this, $matchMethodName)) {
                $matched = $this->$matchMethodName($value);
                if ($matched === null) {
                    continue;
                }
                $result = $result && $matched;
                if ($result === false) {
                    break;
                }
            }
        }
        return $result;
    }

    /**
     * Process `deny` filters
     *
     * @param array $filters
     * @return bool
     */
    protected function processDenyFilters(array $filters)
    {
        $result = true;
        foreach ($filters as $key => $value) {
            $filterMethodName = $key . 'Match';
            if (method_exists($this, $filterMethodName)) {
                $matched = $this->$filterMethodName($value);
                if ($matched === null) {
                    continue;
                }
                $result = $result && !$matched;
                if ($result === false) {
                    break;
                }
            }
        }
        return $result;
    }
}

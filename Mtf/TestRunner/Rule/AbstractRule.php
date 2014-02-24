<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\TestRunner\Rule;

use Mtf\TestRunner\Configuration;
use Mtf\ObjectManager;

/**
 * Class AbstractRule
 *
 * @package Mtf\TestRunner\Rule
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

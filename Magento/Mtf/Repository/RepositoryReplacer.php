<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Repository;

use Magento\Mtf\Config\ReplacerInterface;
use Magento\Mtf\Config\Data;

/**
 * Config replacer for the repository.
 *
 * @api
 */
class RepositoryReplacer implements ReplacerInterface
{
    /**
     * Full field path.
     *
     * @var string
     */
    private $fullFieldPath = '';

    /**
     * Config data object.
     *
     * @var Data
     */
    private $configData;

    /**
     * @constructor
     * @param Data $configData
     */
    public function __construct(Data $configData)
    {
        $this->configData = $configData;
    }

    /**
     * Apply specified node in 'replace' attribute instead of original.
     *
     * @param array $output
     * @return void
     */
    public function apply(&$output)
    {
        foreach ($output as $key => &$value) {
            if (isset($value['replace'])) {
                $replaceKey = $value['replace'];
                unset($value['replace']);
                $output[$replaceKey] = $value;
                unset($output[$key]);
                continue;
            }
            if (is_array($value)) {
                $this->addFullFieldPath($key);
                $this->apply($value);
                $this->removeFullFieldPath($key);
            } else {
                $globalReplaceValue = $this->configData->get($this->fullFieldPath . '/' . $key);
                if (is_string($globalReplaceValue)) {
                    $output[$key] = $globalReplaceValue;
                }
            }
        }
    }

    /**
     * Remove full field path.
     *
     * @param string $key
     */
    private function removeFullFieldPath($key) {
        $start = strlen('/' . $key);
        $this->fullFieldPath = substr_replace($this->fullFieldPath, '', -$start);
    }

    /**
     * Add sub-key to full field path.
     *
     * @param string $key
     * @return void
     */
    private function addFullFieldPath($key) {
        $this->fullFieldPath .= (($this->fullFieldPath == '') ? '' : '/') . $key;
    }
}

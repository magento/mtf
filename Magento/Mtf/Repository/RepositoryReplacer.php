<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Repository;

use Magento\Mtf\Config\ReplacerInterface;

/**
 * Config replacer for the repository.
 *
 * @api
 */
class RepositoryReplacer implements ReplacerInterface
{
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
                $this->apply($value);
            }
        }
    }
}

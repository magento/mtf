<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Config;

/**
 * Config replacer interface.
 */
interface ReplacerInterface
{
    /**
     * Apply specified node in 'replace' attribute instead of original.
     *
     * @param array $output
     * @return array
     */
    public function apply(&$output);
}

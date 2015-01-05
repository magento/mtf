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

namespace Mtf\Util\Filter;

/**
 * Base class filters out classes that are affected by specified tag.
 */
class AbstractFilterTag extends AbstractFilter
{
    /**
     * Value for empty tag.
     */
    const UNDEFINED_TAG = 'UNDEFINED';

    /**
     * Filters out tags.
     *
     * @param array $tags
     * @return bool
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function processApply(array $tags)
    {
        foreach ($this->allow as $group => $groupAllowTags) {
            $groupTags = isset($tags[$group]) ? $tags[$group] : [];
            if ($groupAllowTags && !$this->inTags($groupTags, $groupAllowTags)) {
                return false;
            }
        }
        foreach ($this->deny as $group => $groupDenyTags) {
            $groupTags = isset($tags[$group]) ? $tags[$group] : [];
            if ($groupDenyTags && $this->inTags($groupTags, $groupDenyTags)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Checks that one of tags exists in stack of tags.
     *
     * @param array $needleTags
     * @param array $stackTags
     * @return bool
     */
    protected function inTags(array $needleTags, array $stackTags)
    {
        if (empty($needleTags) && in_array(self::UNDEFINED_TAG, $stackTags)) {
            return true;
        }
        foreach ($needleTags as $needleTag) {
            if (in_array($needleTag, $stackTags)) {
                return true;
            }
        }
        return false;
    }
}

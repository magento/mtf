<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Util\Filter;

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
        $tags = [];
        foreach ($stackTags as $value) {
            $tags = array_merge($tags, explode(', ', $value));
        }

        if (empty($needleTags) && in_array(self::UNDEFINED_TAG, $tags)) {
            return true;
        }
        foreach ($needleTags as $needleTag) {
            if (in_array($needleTag, $tags)) {
                return true;
            }
        }
        return false;
    }
}

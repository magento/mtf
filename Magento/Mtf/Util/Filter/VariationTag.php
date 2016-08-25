<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Util\Filter;

/**
 * Class filters out variations that are affected by specified tag.
 */
class VariationTag extends AbstractFilterTag implements FilterInterface
{
    /**
     * Subject for filter.
     *
     * @var string
     */
    private $subject = 'tag';

    /**
     * Filters out variation.
     *
     * @param string $subject
     * @return bool
     */
    public function apply($subject)
    {
        $tags = $this->convertTags($subject);
        return $this->processApply($tags);
    }

    /**
     * Get subject.
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Convert tags from string to array.
     *
     * @param string $subject
     * @return array
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function convertTags($subject)
    {
        $compositeTags = $subject ? array_map('trim', explode(',', $subject)) : [];
        $result = [];

        foreach ($compositeTags as $compositeTag) {
            $pieces = explode(':', $compositeTag);
            $group = isset($pieces[0]) ? $pieces[0] : '';
            $tag = isset($pieces[1]) ? $pieces[1] : '';

            if (!isset($result[$group])) {
                $result[$group] = [];
            }
            $result[$group][] = $tag;
        }

        return $result;
    }
}

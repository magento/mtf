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

namespace Magento\Mtf\Util\Filter;

/**
 * Class filters out variations that are affected by specified tag.
 */
class VariationTag extends AbstractFilterTag implements FilterInterface
{
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

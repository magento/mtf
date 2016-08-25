<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Util\Filter;

/**
 * Class filters out variations that are affected by specified variation name.
 */
class VariationName extends AbstractFilter implements FilterInterface
{
    /**
     * Subject for filter.
     *
     * @var string
     */
    private $subject = 'variation_name';

    /**
     * Filters out variation.
     *
     * @param string $subject
     * @return bool
     */
    public function apply($subject)
    {
        if ($this->allow) {
            foreach ($this->allow as $allow) {
                if ($allow['value'] == $subject) {
                    return true;
                }
            }

            return false;
        }
        if ($this->deny) {
            foreach ($this->deny as $deny) {
                if ($deny['value'] == $subject) {
                    return false;
                }
            }

            return true;
        }

        return true;
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
}

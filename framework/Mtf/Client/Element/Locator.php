<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Client\Element;

/**
 * Class Locator
 *
 * Locator class is responsible for keeping selector/location data of element on the page.
 *
 * @package Mtf\Client\Element
 */
class Locator extends \ArrayObject
{
    /**#@+
     * Locator strategy (from Selenium)
     */
    const SELECTOR_XPATH        = 'xpath';
    const SELECTOR_CSS          = 'css selector';
    const SELECTOR_ID           = 'id';
    const SELECTOR_NAME         = 'name';
    const SELECTOR_CLASS_NAME   = 'class name';
    const SELECTOR_TAG_NAME     = 'tag name';
    const SELECTOR_LINK_TEXT    = 'link text';
    /**#@-*/

    /**
     * @param string $value
     * @param string $strategy
     */
    public function __construct($value, $strategy = self::SELECTOR_CSS)
    {
        $this['value'] = $value;
        $this['using'] = $strategy;
    }
}

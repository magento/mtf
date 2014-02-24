<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Code\Minifier\Adapter\Js;

if (!class_exists('JSMin')) {
    require_once(__DIR__ . '/../../../../../JSMin/jsmin.php');
}

/**
 * Adapter for JSMin library
 */
class Jsmin implements \Magento\Code\Minifier\AdapterInterface
{
    /**
     * {@inheritdoc}
     */
    public function minify($content)
    {
        return \JSMin::minify($content);
    }
}

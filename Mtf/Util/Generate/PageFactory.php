<?php
/**
 * {license_notice}
 *
 * @api
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Util\Generate;

/**
 * Class PageFactory
 *
 * Page Factory generator
 *
 * @package Mtf\Util\Generate
 */
class PageFactory extends GenerateAbstract
{
    /**
     * Generate Pages
     */
    public function generate()
    {
        $this->_startFactory('Page');

        $this->_generatePages();

        $this->_endTypeFactory('Page');

        echo 'Total Pages found: ' . $this->_cnt . "<br />";
    }

    /**
     * Collect Page items
     */
    protected function _generatePages()
    {
        $items = $this->collectItems('Page');

        foreach ($items as $item) {
            $this->_addPageToFactory($item);
        }
    }

    /**
     * Add Page to content
     *
     * @param array $item
     */
    protected function _addPageToFactory($item)
    {
        $realClass = $this->_resolveClass($item);
        $reflectionClass = new \ReflectionClass($realClass);
        $mca = $reflectionClass->getConstant('MCA');
        $methodNameSuffix = $this->_toCamelCase($mca);

        $fallbackComment = $this->_buildFallbackComment($item);

        $this->_factoryContent .= "\n    /**\n";
        $this->_factoryContent .= "     * @return \\{$item['class']}\n";
        $this->_factoryContent .= "     */\n";
        $this->_factoryContent .= "    public function get{$methodNameSuffix}()\n";
        $this->_factoryContent .= "    {";

        if (!empty($fallbackComment)) {
            $this->_factoryContent .= $fallbackComment . "\n";
        } else {
            $this->_factoryContent .= "\n";
        }

        $this->_factoryContent .= "        return new \\{$realClass}(\$this->_configuration);";
        $this->_factoryContent .= "\n    }\n";

        $this->_cnt++;
    }
}

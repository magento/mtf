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
 * Class BlockFactory
 *
 * Block Factory generator
 *
 * @package Mtf\Util\Generate
 */
class BlockFactory extends GenerateAbstract
{
    /**
     * Generate Blocks
     */
    public function generate()
    {
        $this->_startFactory('Block');

        $this->_generateBlocks();

        $this->_endTypeFactory('Block');

        echo 'Total Blocks found: ' . $this->_cnt . "<br />";
    }

    /**
     * Collect Block items
     */
    protected function _generateBlocks()
    {
        $blocks = $this->collectItems('Block');
        foreach ($blocks as $block) {
            $this->_addBlockToFactory($block);
        }
    }

    /**
     * Add Block to content
     *
     * @param array $item
     */
    protected function _addBlockToFactory($item)
    {
        list($module, $name) = explode('Test\\Block', $item['class']);
        $methodNameSuffix = $module . $name;
        $methodNameSuffix = $this->_toCamelCase($methodNameSuffix);

        $realClass = $this->_resolveClass($item);
        $fallbackComment = $this->_buildFallbackComment($item, '$element');

        $this->_factoryContent .= "\n    /**\n";
        $this->_factoryContent .= "     * @return \\{$item['class']}\n";
        $this->_factoryContent .= "     */\n";
        $this->_factoryContent .= "    public function get{$methodNameSuffix}(\$element, \$driver = null)\n";
        $this->_factoryContent .= "    {";

        if (!empty($fallbackComment)) {
            $this->_factoryContent .= $fallbackComment . "\n";
        } else {
            $this->_factoryContent .= "\n";
        }

        $this->_factoryContent .= '        return new \\' . $realClass . '($element, $driver);';
        $this->_factoryContent .= "\n    }\n";

        $this->_cnt++;
    }
}

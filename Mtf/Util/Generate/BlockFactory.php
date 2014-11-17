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

namespace Mtf\Util\Generate;

/**
 * Class BlockFactory
 * Block Factory generator
 */
class BlockFactory extends GenerateAbstract
{
    /**
     * Generate Blocks
     *
     * @return void
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
     *
     * @return void
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
     * @return void
     */
    protected function _addBlockToFactory($item)
    {
        list($module, $name) = explode('Test\\Block', $item['class']);
        $methodNameSuffix = $module . $name;
        $methodNameSuffix = $this->_toCamelCase($methodNameSuffix);

        $realClass = $this->_resolveClass($item);
        $fallbackComment = $this->_buildFallbackComment($item, '$element');

        $arguments = "\$element, \$driver = null, \$config = []";
        $this->_factoryContent .= "\n    /**\n";
        $this->_factoryContent .= "     * @return \\{$item['class']}\n";
        $this->_factoryContent .= "     */\n";
        $this->_factoryContent .= "    public function get{$methodNameSuffix}({$arguments})\n";
        $this->_factoryContent .= "    {";

        if (!empty($fallbackComment)) {
            $this->_factoryContent .= $fallbackComment . "\n";
        } else {
            $this->_factoryContent .= "\n";
        }

        $this->_factoryContent .= '        return new \\' . $realClass . '($element, $driver, $config);';
        $this->_factoryContent .= "\n    }\n";

        $this->_cnt++;
    }
}

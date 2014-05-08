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
 * Class RepositoryFactory
 *
 * Fixture Factory generator
 *
 * @package Mtf\Util\Generate
 */
class RepositoryFactory extends GenerateAbstract
{
    /**
     * Generate Fixtures
     * @return void
     */
    public function generate()
    {
        $this->_startFactory('Repository');

        $this->_generateFixtureRepositories();

        $this->_endTypeFactory('Repository');

        echo 'Total Repositories found: ' . $this->_cnt . "<br />";
    }

    /**
     * Collect Fixture Repository items
     * @return void
     */
    protected function _generateFixtureRepositories()
    {
        $items = $this->collectItems('Repository');

        foreach ($items as $item) {
            $this->_addToFactory($item);
        }
    }

    /**
     * Add Fixture Repository
     *
     * @param array $item
     * @return void
     */
    protected function _addToFactory($item)
    {
        list($module, $name) = explode('Test\\Repository', $item['class']);
        $methodNameSuffix = $module . $name;
        $methodNameSuffix = $this->_toCamelCase($methodNameSuffix);

        $realClass = $this->_resolveClass($item);
        $fallbackComment = $this->_buildFallbackComment($item);

        $this->_factoryContent .= "\n    /**\n";
        $this->_factoryContent .= "     * @return \\{$item['class']}\n";
        $this->_factoryContent .= "     */\n";
        $this->_factoryContent .= "    public function get{$methodNameSuffix}(array \$dataConfig, array \$data)\n";
        $this->_factoryContent .= "    {";

        if (!empty($fallbackComment)) {
            $this->_factoryContent .= $fallbackComment . "\n";
        } else {
            $this->_factoryContent .= "\n";
        }

        $this->_factoryContent .= "        return new \\{$realClass}(\$dataConfig, \$data);\n";
        $this->_factoryContent .= "    }\n";

        $this->_cnt++;
    }
}

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
 * Class FixtureFactory
 * Fixture Factory generator
 */
class FixtureFactory extends GenerateAbstract
{
    /**
     * Fixture factory content
     *
     * @var string
     */
    protected $_fixtureFactoryContent = '';

    /**
     * Generate Fixtures
     *
     * @return void
     */
    public function generate()
    {
        $this->_startFactory('Fixture');

        $this->_generateFixtures();

        $this->_endTypeFactory('Fixture');

        echo 'Total Fixtures found: ' . $this->_cnt . "<br />";
    }

    /**
     * Collect Fixture items
     *
     * @return void
     */
    protected function _generateFixtures()
    {
        $items = $this->collectItems('Fixture');
        foreach ($items as $item) {
            $this->_addFixtureToFactory($item);
        }
    }

    /**
     * Add Fixture content
     *
     * @param array $item
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _addFixtureToFactory($item)
    {
        list($module, $name) = explode('Test\\Fixture', $item['class']);
        $methodNameSuffix = $module . $name;
        $methodNameSuffix = $this->_toCamelCase($methodNameSuffix);
        $realClass = $this->_resolveClass($item);
        $fallbackComment = $this->_buildFallbackComment($item);

        $parameters = array();
        $arguments = array('$this->_configuration');

        $refClass = new \ReflectionClass($item['class']);
        $constructor = $refClass->getConstructor();
        foreach ($constructor->getParameters() as $param) {
            // skip first configuration parameter
            if ($param->getPosition() == 0) {
                continue;
            }
            $parameter = '';
            $argument = '';
            $paramClass = $param->getClass();
            if (null !== $paramClass) {
                $parameter .= '\\' . $paramClass->getName() . ' ';
            } elseif ($param->isArray()) {
                $parameter .= 'array ';
            } elseif (method_exists($param, 'isCallable') && $param->isCallable()) {
                $parameter .= 'callable ';
            }
            if ($param->isPassedByReference()) {
                $parameter .= '&';
            }

            $parameter .= '$' . $param->getName();
            $argument .= '$' . $param->getName();

            if ($param->isDefaultValueAvailable()) {
                $parameter .= ' = ' . var_export($param->getDefaultValue(), true);
            }
            $parameters[] = $parameter;
            $arguments[] = $argument;
        }
        $parameterString = implode(', ', str_replace("\n", '', $parameters));
        $argumentString = implode(', ', $arguments);

        $this->_factoryContent .= "\n";
        $this->_factoryContent .= "    /**\n";
        $this->_factoryContent .= "     * @return \\{$item['class']}\n";
        $this->_factoryContent .= "     */\n";
        $this->_factoryContent .= "    public function get{$methodNameSuffix}($parameterString)\n";
        $this->_factoryContent .= "    {";

        if (!empty($fallbackComment)) {
            $this->_factoryContent .= $fallbackComment . "\n";
        } else {
            $this->_factoryContent .= "\n";
        }

        $this->_factoryContent .= "        return new \\{$realClass}($argumentString);\n";
        $this->_factoryContent .= "    }\n";

        $this->_cnt++;
    }
}

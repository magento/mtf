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
 * Class FixtureFactory
 *
 * Fixture Factory generator
 *
 * @package Mtf\Util\Generate
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

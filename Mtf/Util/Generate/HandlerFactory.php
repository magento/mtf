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
 * Class HandlerFactory
 *
 * Handler Factory generator
 *
 * @package Mtf\Util\Generate
 */
class HandlerFactory extends GenerateAbstract
{
    /**
     * Generate Handlers
     */
    public function generate()
    {
        $this->_startFactory('Handler');

        $this->_generateHandlers();

        $this->_endTypeFactory('Handler');

        echo 'Total Handlers found: ' . $this->_cnt . "<br />";
    }

    /**
     * Collect Handler items
     */
    protected function _generateHandlers()
    {
        $items = $this->collectItems('Handler');

        $generatorConfig = $this->_params['generator_config'];
        $fallback = $generatorConfig['handler_fallback'];

        $byTypes = array();
        foreach ($items as $item) {
            preg_match('/(\w*)\\\(\w*)\\\Test\\\Handler\\\(\w*)\\\(\w*)/', $item['class'], $matches);
            if (5 === count($matches)) {
                $methodNameSuffix = strtolower($matches[1]) . $matches[2] . $matches[4];
                foreach ($fallback as $pos => $type) {
                    if ($matches[3] === $type) {
                        if (!isset($byTypes[$methodNameSuffix])) {
                            $item['_fallback_position_'] = $pos;
                            $byTypes[$methodNameSuffix] = $item;
                        } else {
                            $_item = $byTypes[$methodNameSuffix];
                            $_pos = $_item['_fallback_position_'];
                            if ($_pos > $pos) {
                                $item['_fallback_position_'] = $pos;
                                $byTypes[$methodNameSuffix] = $item;
                            }
                        }
                        break;
                    }
                }
            }
        }

        foreach ($byTypes as $methodNameSuffix => $item) {
            $this->_addHandlerToFactory($methodNameSuffix, $item);
        }
    }

    /**
     * Add Handler content
     *
     * @param string $methodNameSuffix
     * @param array $item
     */
    protected function _addHandlerToFactory($methodNameSuffix, $item)
    {
        $fallbackComment = $this->_buildFallbackComment($item);
        $realClass = $this->_resolveClass($item);

        $this->_factoryContent .= "\n    /**\n";
        $this->_factoryContent .= "     * @return \\{$item['class']}\n";
        $this->_factoryContent .= "     */\n";
        $this->_factoryContent .= "    public function {$methodNameSuffix}(Fixture \$fixture = null)\n";
        $this->_factoryContent .= "    {";

        if (!empty($fallbackComment)) {
            $this->_factoryContent .= $fallbackComment . "\n";
        } else {
            $this->_factoryContent .= "\n";
        }

        $this->_factoryContent .= "        \$handler = new \\{$realClass}(\$this->_configuration);\n";
        $this->_factoryContent .= "        return \$handler->execute(\$fixture);";
        $this->_factoryContent .= "\n    }\n";

        $this->_cnt++;
    }

    /**
     * Add header content
     *
     * @param string $type
     */
    protected function _startFactory($type)
    {
        $this->_factoryContent = "<?php\n\n";
        $this->_factoryContent .= "namespace Mtf\\{$type}; \n\n";
        $this->_factoryContent .= "use Mtf\\System\\Config; \n\n";
        $this->_factoryContent .= "use Mtf\\Fixture; \n\n";
        $this->_factoryContent .= "class {$type}Factory\n";
        $this->_factoryContent .= "{\n";

        $this->_factoryContent .= "    /**
     * Configuration
     *
     * @var Config
     */
    protected \$_configuration;

    /**
     * Constructor
     *
     * @param Config \$configuration
     */
    public function __construct(Config \$configuration)
    {
        \$this->_configuration = \$configuration;
    }\n";
    }
}

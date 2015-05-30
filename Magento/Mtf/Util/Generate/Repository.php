<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\Util\Generate;

/**
 * Repository classes generator.
 */
class Repository extends AbstractGenerate
{
    /**
     * Launch generation of all repository classes.
     *
     * @return void
     */
    public function launch()
    {
        $this->cnt = 0;

        foreach ($this->configData->get('repository') as $name => $data) {
            $this->generateClass($name, $data);
        }

        \Magento\Mtf\Util\Generate\GenerateResult::addResult('Repository Classes', $this->cnt);
    }

    /**
     * Generate single repository class.
     *
     * @param string $className
     * @return string|bool
     * @throws \InvalidArgumentException
     */
    public function generate($className)
    {
        if (!$this->configData->get('repository/' . $className)) {
            throw new \InvalidArgumentException('Invalid class name: ' . $className);
        }

        return $this->generateClass(
            $className, $this->configData->get('repository/' . $className)
        );
    }

    /**
     * Generate repository class from XML source.
     *
     * @param string $class
     * @param array $data
     * @return string|bool
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function generateClass($class, array $data)
    {
        $className = $this->getShortClassName($class);
        $ns = $this->getNamespace($class);

        $content = "<?php\n";
        $content .= $this->getFilePhpDoc();
        $content .= "namespace {$ns};\n\n";
        $content .= "use Magento\\Mtf\\Repository\\AbstractRepository;\n\n";
        $content .= "/**\n";
        $content .= " * Class {$className}\n";
        $content .= " */\n";
        $content .= "class {$className} extends AbstractRepository\n";
        $content .= "{\n";

        $content .= "    /**\n     * @constructor\n";
        $content .= "     * @param array \$defaultConfig\n";
        $content .= "     * @param array \$defaultData\n     */\n";
        $content .= '    public function __construct(array $defaultConfig = [], array $defaultData = [])' . "\n";
        $content .= "    {\n";
        $dataSets = $data['dataset'];
        end($dataSets);
        $lastItemName = key($dataSets);
        foreach ($dataSets as $name => $item) {
            $content .= "        \$this->_data['{$name}'] = ";
            $content .= $this->generateArray('', $item['field'], '        ');
            $content .= "        ];\n";
            $content .= $lastItemName === $name ? "" : "\n";
        }
        $content .= "    }\n";
        $content .= "}\n";

        return $this->createClass($class, $content);
    }

    /**
     * Generate dataSet array.
     *
     * @param string $arrayKey
     * @param array $params
     * @param string $indent
     * @param bool $flag
     * @return string
     */
    protected function generateArray($arrayKey, array $params, $indent = '', $flag = false)
    {
        $content = $arrayKey === '' ? "[\n" : $indent . "'{$arrayKey}' => [\n";
        foreach ($params as $key => $value) {
            $content .= is_array($value)
                ? $this->generateArray($key, $value, $indent . '    ', true)
                : ($indent . "    '{$key}' => '" . $value . "',\n");
        }
        $content .= !$flag ? '' : $indent . "],\n";

        return $content;
    }
}

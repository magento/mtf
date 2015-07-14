<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\Util\Generate;

/**
 * Fixture classes generator.
 */
class Fixture extends AbstractGenerate
{
    /**
     * Launch generation of all fixture classes.
     *
     * @return void
     */
    public function launch()
    {
        $this->cnt = 0;

        foreach ($this->configData->get('fixture') as $name => $data) {
            $this->generateClass($data);
        }

        \Magento\Mtf\Util\Generate\GenerateResult::addResult('Fixture Classes', $this->cnt);
    }

    /**
     * Generate single fixture class.
     *
     * @param string $className
     * @return string|bool
     * @throws \InvalidArgumentException
     */
    public function generate($className)
    {
        $classNameParts = explode('\\', $className);
        $classDataKey = 'fixture/' . lcfirst(end($classNameParts));

        if (!$this->configData->get($classDataKey)) {
            throw new \InvalidArgumentException('Invalid class name: ' . $className);
        }

        return $this->generateClass(
            $this->configData->get($classDataKey)
        );
    }

    /**
     * Generate fixture class from XML source.
     *
     * @param array $item
     * @return string|bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function generateClass(array $item)
    {
        $class = $item['class'];
        $className = $this->getShortClassName($class);
        $repository = isset($item['repository_class']) ? $item['repository_class'] : null;
        $handlerInterface = isset($item['handler_interface']) ? $item['handler_interface'] : null;

        //Resolve arguments
        $dataConfig = null;
        if (isset($item['data_config'])) {
            $dataConfig = $item['data_config'][0]['item'];
        }
        $fields = isset($item['field']) ? $item['field'] : [];

        $extends = isset($item['extends']) ? $item['extends'] : '\Magento\Mtf\Fixture\InjectableFixture';
        $phpDocVarString = "    /**\n     * @var string\n     */\n";
        $phpDocVarArray = "    /**\n     * @var array\n     */\n";
        $phpDocReturnMixed = "    /**\n     * @return mixed\n     */\n";

        $content = "<?php\n";
        $content .= $this->getFilePhpDoc();
        $content .= "namespace {$this->getNamespace($class)};\n\n";
        $content .= "/**\n";
        $content .= " * Class {$className}\n";
        $content .= " */\n";
        $content .= "class {$className} extends {$extends}\n";
        $content .= "{\n";

        if (isset($repository)) {
            $content .= $phpDocVarString;
            $content .= "    protected \$repositoryClass = '{$repository}';\n\n";
        }
        if (isset($handlerInterface)) {
            $content .= $phpDocVarString;
            $content .= "    protected \$handlerInterface = '{$handlerInterface}';\n\n";
        }
        if (isset($dataConfig)) {
            if (is_array($dataConfig)) {
                $content .= $phpDocVarArray;
                $content .= "    protected \$dataConfig = ";
                $content .= $this->toArrayDefinition($dataConfig, '    ');
                $content .= ";\n\n";
            }
        }

        foreach ($fields as $name => $field) {
            $content .= "\n" . $phpDocVarArray;
            $content .= "    protected \${$name} = [\n";
            foreach ($field as $key => $value) {
                $content .= "        '{$key}' => '{$value}',\n";
            }
            $content .= "    ];\n";
        }
        foreach ($fields as $name => $field) {
            $content .= "\n" . $phpDocReturnMixed;
            $content .= '    public function get' . $this->toCamelCase($name) . '()' . "\n";
            $content .= "    {\n";
            $content .= "        return \$this->getData('" . $name . "');" . "\n";
            $content .= "    }\n";
        }
        $content .= "}\n";

        return $this->createClass($class, $content);
    }

    /**
     * Convert array to string.
     *
     * @param array $array
     * @param string $tab
     * @return string
     */
    protected function toArrayDefinition(array $array, $tab = '')
    {
        $result = "[\n";
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $value = $this->toArrayDefinition($value, $tab . '    ');
                $result .= $tab . "    '{$key}' => {$value},\n";
            } else {
                $result .= $tab . "    '{$key}' => '{$value}',\n";
            }
        }
        $result .= $tab . "]";
        return $result;
    }
}

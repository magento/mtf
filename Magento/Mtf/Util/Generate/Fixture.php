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

namespace Magento\Mtf\Util\Generate;

use Magento\Mtf\ObjectManagerInterface;
use Magento\Mtf\Repository\Reader\Converter;
use Magento\Mtf\Config\DataInterface;

/**
 * Fixture files generator.
 *
 * @internal
 */
class Fixture extends AbstractGenerate
{
    /**
     * @var DataInterface
     */
    protected $configData;

    /**
     * @constructor
     * @param ObjectManagerInterface $objectManager
     * @param Converter $repositoryConverter
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        DataInterface $configData,
        Converter $repositoryConverter
    ) {
        parent::__construct($objectManager);
        $this->configData = $configData;
        $this->repositoryConverter = $repositoryConverter;
    }

    /**
     * Launch Fixture generators.
     *
     * @return void
     */
    public function launch()
    {
        $this->generateClasses();
    }

    /**
     * Generate Fixtures Classes.
     *
     * @return void
     */
    protected function generateClasses()
    {
        $this->cnt = 0;
        $fixtures = $this->configData->get('fixture');
        foreach ($fixtures as $key => $fixtureData) {
            $this->generateClass($fixtureData);
        }
        \Magento\Mtf\Util\Generate\GenerateResult::addResult('Fixture Classes', $this->cnt);
    }

    protected function resolveArguments($arguments)
    {
        $output = [];
        if (isset($arguments['item'])) {
            $arguments = $arguments['item'];
        }
        foreach ($arguments as $key => $item) {
            if (isset($item['xsi:type']) && $item['xsi:type'] == 'array') {
                $output[$key] = $this->resolveArguments($item);
            } else if (is_array($item)){
                $output[$key] = $item['value'];
            }
        }
        return $output;
    }

    /**
     * Generate fixture classes from sources.
     *
     * @param array $item
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function generateClass(array $item)
    {
        $class = $item['class'];
        $classNameArray = explode('\\', $class);
        $className = end($classNameArray);
        $fileName = $className . '.php';
        $relativeFilePath = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
        $relativeFolderPath = str_replace(DIRECTORY_SEPARATOR . $fileName, '', $relativeFilePath);

        $ns = implode("\\", array_slice($classNameArray, 0, -1));
        $repository = isset($item['repository_class']) ? $item['repository_class'] : null;
        $handlerInterface = isset($item['handler_interface']) ? $item['handler_interface'] : null;

        //Resolve arguments
        $dataConfig = null;
        if (isset($item['data_config'])) {
            $dataConfig = $this->resolveArguments($item['data_config'][0]);
        }
        $fields = isset($item['field']) ? $item['field'] : [];
        $defaultDataSet = [];
        if (isset($item['dataset'])) {
            foreach ($item['dataset']['default']['field'] as $key => $value) {
                $defaultDataSet[$key] = $this->getDefaultValue($value);
            }
        } else {
            $defaultDataSet = $this->getDefaultValues((array)$fields);
        }

        $extends = isset($item['extends']) ? $item['extends'] : '\Magento\Mtf\Fixture\InjectableFixture';
        $phpDocVarString = "    /**\n     * @var string\n     */\n";
        $phpDocVarArray = "    /**\n     * @var array\n     */\n";
        $phpDocReturnMixed = "    /**\n     * @return mixed\n     */\n";

        $content = "<?php\n";
        $content .= $this->getFilePhpDoc();
        $content .= "namespace {$ns};\n\n";
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

        $content .= $phpDocVarArray;
        $content .= "    protected \$defaultDataSet = ";
        $content .= $this->generateArray('', $defaultDataSet, '    ');
        $content .= "    ];\n";
        foreach ($fields as $name => $field) {
            $content .= "\n" . $phpDocVarArray;
            $content .= "    protected \${$name} = [\n";
            foreach ($field as $key => $value) {
                if ($key == 'default_value') {
                    $value = $this->getDefaultValue($value[0]);
                }
                if (is_array($value)) {
                    $content .= "        '{$key}' => ";
                    $content .= $this->generateArray('', $value, '        ');
                    $content .= "        ],\n";
                } else {
                    $content .= "        '{$key}' => '{$value}',\n";
                }
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

        $filePath = MTF_BP . '/generated/' . $relativeFilePath;
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $folderPath = MTF_BP . '/generated/' . $relativeFolderPath;
        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        file_put_contents($filePath, $content);
        $this->cnt++;
    }

    /**
     * Get default values of all fields.
     *
     * @param array $fields
     * @return array
     */
    protected function getDefaultValues(array $fields)
    {
        $data = [];
        foreach ($fields as $name => $field) {
            if (empty($field['default_value'])) {
                continue;
            }
            $data[$name] = $this->getDefaultValue($field['default_value'][0]);
        }
        return $data;
    }

    protected function getDefaultValue($data)
    {
        if ($data['xsi:type'] == 'null') {
            return null;
        } else if ($data['xsi:type'] == 'array') {
            return $this->resolveArguments($data);
        } else {
            return $data['value'];
        }
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

    /**
     * Generate dataSet array.
     *
     * @param string $arrayKey
     * @param array|string $params
     * @param string $indent
     * @param bool $flag
     * @return string
     */
    protected function generateArray($arrayKey, $params, $indent = '', $flag = false)
    {
        $content = $arrayKey == '' ? "[\n" : $indent . "'{$arrayKey}' => [\n";
        foreach ($params as $key => $value) {
            $content .= is_array($value)
                ? $this->generateArray($key, $value, $indent . '    ', true)
                : ($indent . "    '{$key}' => '" . $value . "',\n");
        }
        $content .= !$flag ? '' : $indent . "],\n";

        return $content;
    }
}

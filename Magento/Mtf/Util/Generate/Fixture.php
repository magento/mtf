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

use Magento\Mtf\Util\Generate\Fixture\FieldsProviderInterface;
use Magento\Mtf\ObjectManagerInterface;
use Magento\Mtf\Util\XmlConverter;
use Magento\Mtf\Config\FileResolver\Module;
use Magento\Mtf\Util\Generate\Fixture\Reader;
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
     * File Resolver.
     *
     * @var Module
     */
    protected $fileResolver;

    /**
     * Converter xml data to array.
     *
     * @var XmlConverter.
     */
    protected $xmlConverter;

    /**
     * Fixture reader.
     *
     * @var Reader
     */
    protected $fixtureReader;

    /**
     * @constructor
     * @param ObjectManagerInterface $objectManager
     * @param Module $fileResolver
     * @param FieldsProviderInterface $fieldsProvider
     * @param XmlConverter $xmlConverter
     * @param Reader $fixtureReader
     * @param Converter $repositoryConverter
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Module $fileResolver,
//       DataInterface $configData,
        FieldsProviderInterface $fieldsProvider,
        XmlConverter $xmlConverter,
        Reader $fixtureReader,
        Converter $repositoryConverter
    ) {
        parent::__construct($objectManager);
        $this->fileResolver = $fileResolver;
//        $this->configData = $configData;
        $this->fieldsProvider = $fieldsProvider;
        $this->xmlConverter = $xmlConverter;
        $this->fixtureReader = $fixtureReader;
        $this->repositoryConverter = $repositoryConverter;
    }

    /**
     * Launch Fixture generators.
     *
     * @return void
     */
    public function launch()
    {
        $this->generateXml();
        $this->generateClasses();
    }

    /**
     * Generate Fixtures XML.
     *
     * @return void
     */
    protected function generateXml()
    {
        $this->cnt = 0;
        if (!$this->fieldsProvider->checkConnection()) {
            \Magento\Mtf\Util\Generate\GenerateResult::addResult('Fixture XML Files', $this->cnt);
            return;
        }
        // @TODO fix it
//        foreach ($this->configData->get() as $name => $item) {
//            $this->generateFixtureXml($name, $item);
//        }
        $configs = $this->fileResolver->get('fixture.xml', 'etc');
        foreach ($configs as $config) {
            $configXml = simplexml_load_string($config);

            if ($configXml instanceof \SimpleXMLElement) {
                $fixtures = $this->xmlConverter->convert($configXml);

                foreach ($fixtures as $code => $fixture) {
                    $this->generateFixtureXml($code, $fixture);
                }
            }
        }
        \Magento\Mtf\Util\Generate\GenerateResult::addResult('Fixture XML Files', $this->cnt);
    }

    /**
     * Generate fixtures XML definition files.
     *
     * @param string $name
     * @param array $item
     * @return void
     */
    protected function generateFixtureXml($name, array $item)
    {
        $classShortName = ucfirst($name);
        $fileName = $classShortName . '.xml';
        $moduleName = $item['module'];
        $path = str_replace('_', '/', $moduleName) . '/Test/Fixture';
        $className = str_replace('_', '\\', $moduleName) . '\\Test\\Fixture\\' . $classShortName;
        $folderName = MTF_TESTS_PATH . $path;
        if (file_exists($folderName . '/' . $fileName)) {
            return;
        }
        if (!is_dir($folderName)) {
            mkdir($folderName, 0777, true);
        }
        $content = '<?xml version="1.0" ?>' . "\n";
        $content .= '<!--' . "\n";
        $content .= $this->getFilePhpDoc();
        $content .= '-->' . "\n";
        $content .= '<fixture class="' . $className . '"' . "\n";
        $content .= '         xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . "\n";
        $content .= '         xsi:noNamespaceSchemaLocation="';
        $content .= '../../../../../../vendor/magento/mtf/Magento/Mtf/Fixture/etc/fixture.xsd">' . "\n";

        $fields = $this->fieldsProvider->getFields($item);
        if (!empty($item['fields']) && is_array($item['fields'])) {
            $fields = array_merge($fields, $item['fields']);
        }
        $item['fields'] = $fields;

        if (!isset($item['repository_class'])) {
            $item['repository_class'] = str_replace('_', '\\', $moduleName) . '\\Test\\Repository\\' . $classShortName;
        }
        if (!isset($item['handler_interface'])) {
            $item['handler_interface'] = str_replace('_', '\\', $moduleName)
                . '\\Test\\Handler\\'
                . $classShortName
                . '\\' . $classShortName . 'Interface';
        }

        $content .= $this->toXml($item, '    ');

        $content .= "</fixture>\n";
        file_put_contents($folderName . '/' . $fileName, $content);
        $this->cnt++;
    }

    /**
     * Generate Fixtures Classes.
     *
     * @return void
     */
    protected function generateClasses()
    {
        $this->cnt = 0;
        $fixtures = $this->collectFixturesXml();

        foreach ($fixtures as $fixtureData) {
            $fixture = $this->mergeFixtureXml($fixtureData);
            $this->generateClass($fixture);
        }

        \Magento\Mtf\Util\Generate\GenerateResult::addResult('Fixture Classes', $this->cnt);
    }

    /**
     * Collect all fixtures .xml files.
     *
     * @return array
     */
    protected function collectFixturesXml()
    {
        $items = [];
        $path = MTF_TESTS_PATH . '*/*';
        $modules = glob($path);
        foreach ($modules as $modulePath) {
            $modulePathArray = explode('/', $modulePath);
            $module = array_pop($modulePathArray);
            $namespace = array_pop($modulePathArray);
            if (!is_readable($modulePath . '/Test/Fixture')) {
                continue;
            }
            $dirIterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(
                    $modulePath . '/Test/Fixture',
                    \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::FOLLOW_SYMLINKS
                )
            );
            foreach ($dirIterator as $fileInfo) {
                /** @var $fileInfo \SplFileInfo */
                $fileExt = $fileInfo->getExtension();
                if ($fileExt === 'xml') {
                    $fileName = $fileInfo->getBasename('.xml');
                    $items[$fileName][] = [
                        'file_name' => $fileName,
                        'module_path' => str_replace('\\', '/', $modulePath),
                        'folder_path' => str_replace('\\', '/', $fileInfo->getPath()),
                        'real_path' => str_replace('\\', '/', $fileInfo->getRealPath()),
                        'module' => $module,
                        'namespace' => $namespace
                    ];
                }
            }
        }
        return $items;
    }

    /**
     * Merge xml fixture data.
     *
     * @param array $fixtureData
     * @return array
     */
    protected function mergeFixtureXml(array $fixtureData)
    {
        $config = [];

        foreach ($fixtureData as $file) {
            $config = array_replace_recursive($config, $this->fixtureReader->read($file['real_path']));
        }

        return $config;
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
        $dataConfig = isset($item['data_config']) ? $item['data_config'] : null;
        $fields = isset($item['fields']) ? $item['fields'] : [];
        $defaultDataSet = isset($item['dataset']) ? $item['dataset'] : $this->getDefaultValues((array)$fields);
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
            $data[$name] = $field['default_value'];
        }

        return $data;
    }

    /**
     * Convert array to xml string.
     *
     * @param array $data
     * @param string $tab
     * @param bool $fields
     * @param string $nodeName
     * @return string
     */
    protected function toXml(array $data, $tab, $fields = false, $nodeName = '')
    {
        $nodeList = ['fields', 'entities', 'data_config'];
        $entityType = $tab . "<entity name=\"%s\" />\n";
        $arrayField = $tab . "<field name=\"%s\">\n%s" . $tab . "</field>\n";
        $stringField = $tab . "<field name=\"%s\">%s</field>\n";
        $array = $tab . "<%s>\n%s" . $tab . "</%s>\n";
        $string = $tab . "<%s>%s</%s>\n";
        $xml = '';
        foreach ($data as $fieldName => $fieldValue) {
            if (is_array($fieldValue)) {
                $fieldValue = $this->toXml($fieldValue, $tab . '    ', in_array($fieldName, $nodeList), $fieldName);
                $fieldNameWithAttributes = $fieldName === 'default_value' ? $fieldName . ' xsi:type="array"' : $fieldName;
                $xml .= $fields
                    ? sprintf($arrayField, $fieldName, $fieldValue)
                    : sprintf($array, $fieldNameWithAttributes, $fieldValue, $fieldName);
            } else {
                $fieldNameWithAttributes = $fieldName === 'default_value' ? $fieldName . ' xsi:type="string"' : $fieldName;
                if ($fields) {
                    $xml .= $nodeName === 'entities'
                        ? sprintf($entityType, $fieldValue)
                        : sprintf($stringField, $fieldName, $fieldValue);
                } else {
                    $xml .= sprintf($string, $fieldNameWithAttributes, $fieldValue, $fieldName);
                }
            }
        }
        return $xml;
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

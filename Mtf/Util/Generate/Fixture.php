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

use Mtf\Util\Generate\Fixture\FieldsProviderInterface;
use Mtf\Configuration\Reader;
use Magento\Framework\ObjectManagerInterface;
use Mtf\Util\XmlConverter;

use Mtf\Config\FileResolver\Module;

/**
 * Fixture files generator.
 *
 * @internal
 */
class Fixture extends AbstractGenerate
{
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
     * @constructor
     * @param ObjectManagerInterface $objectManager
     * @param Module $fileResolver
     * @param FieldsProviderInterface $fieldsProvider
     * @param XmlConverter $xmlConverter
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Module $fileResolver,
        FieldsProviderInterface $fieldsProvider,
        XmlConverter $xmlConverter
    ) {
        parent::__construct($objectManager);
        $this->fileResolver = $fileResolver;
        $this->fieldsProvider = $fieldsProvider;
        $this->xmlConverter = $xmlConverter;
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
        \Mtf\Util\Generate\GenerateResult::addResult('Fixture XML Files', $this->cnt);
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
            //unlink($folderName . '/' . $fileName);
            return;
        }
        if (!is_dir($folderName)) {
            mkdir($folderName, 0777, true);
        }
        $content = '<?xml version="1.0" ?>' . "\n";
        $content .= '<!--' . "\n";
        $content .= $this->getFilePhpDoc();
        $content .= '-->' . "\n";
        $content .= '<fixture class="' . $className . '">' . "\n";

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

        \Mtf\Util\Generate\GenerateResult::addResult('Fixture Classes', $this->cnt);
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
            $content = file_get_contents($file['real_path']);
            $configXml = simplexml_load_string($content);

            if ($configXml instanceof \SimpleXMLElement) {
                $config = array_replace_recursive($config, $this->xmlConverter->convert($configXml));
            }
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

        if (file_exists(MTF_BP . '/tests/app/' . $relativeFilePath)) {
            return;
        }

        $ns = str_replace('\\' . $className, '', $class);
        $repository = isset($item['repository']) ? $item['repository'] : null;
        $handlerInterface = isset($item['handler_interface']) ? $item['handler_interface'] : null;
        $dataConfig = isset($item['data_config']) ? $item['data_config'] : null;
        $fields = isset($item['fields']) ? $item['fields'] : [];

        $content = "<?php\n";
        $content .= $this->getFilePhpDoc();
        $content .= "namespace {$ns};\n\n";
        $content .= "use Mtf\\Fixture\\InjectableFixture;\n\n";
        $content .= "/**\n";
        $content .= " * Class {$className}\n";
        $content .= " */\n";
        $content .= "class {$className} extends InjectableFixture\n";
        $content .= "{\n";

        if (isset($repository)) {
            $content .= "    /**\n";
            $content .= "     * @var string\n";
            $content .= "     */\n";
            $content .= "    protected \$repositoryClass = '{$repository}';\n\n";
        }
        if (isset($handlerInterface)) {
            $content .= "    /**\n";
            $content .= "     * @var string\n";
            $content .= "     */\n";
            $content .= "    protected \$handlerInterface = '{$handlerInterface}';\n\n";
        }
        if (isset($dataConfig)) {
            if (is_array($dataConfig)) {
                $content .= "    protected \$dataConfig = ";
                $content .= $this->toArrayDefinition($dataConfig, '    ');
                $content .= ";\n\n";
            }
        }

        $content .= "    protected \$defaultDataSet = [\n";
        foreach ($fields as $name => $field) {
            if (empty($field['default_value'])) {
                continue;
            }
            $content .= "        '{$name}' => null,\n";
        }
        $content .= "    ];\n";
        foreach ($fields as $name => $field) {
            $content .= "\n    protected \${$name} = [\n";
            foreach ($field as $key => $value) {
                $content .= "        '{$key}' => '{$value}',\n";
            }
            $content .= "    ];\n";
        }
        foreach ($fields as $name => $field) {
            $content .= "\n" . '    public function get' . $this->toCamelCase($name) . '()' . "\n";
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
     * Convert array to xml string.
     *
     * @param array $data
     * @param string $tab
     * @param string $tag
     * @return string
     */
    protected function toXml(array $data, $tab, $tag = '')
    {
        $xml = '';
        foreach ($data as $fieldName => $fieldValue) {
            if (is_array($fieldValue)) {
                $fieldValue = $this->toXml($fieldValue, $tab . '    ');
                $xml .= $tab . "<{$fieldName}>\n";
                $xml .= $fieldValue;
                $xml .= $tab . "</{$fieldName}>\n";
            } else {
                $xml .= $tab . "<{$fieldName}>{$fieldValue}</{$fieldName}>\n";
            }
        }
        if ($tag) {
            $xml = $tab . "<{$tag}>\n"
                . $xml . "\n"
                . $tab . "</{$tag}>\n";
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
}

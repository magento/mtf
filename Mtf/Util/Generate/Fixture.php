<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Util\Generate;

use Mtf\Util\Generate\Fixture\FieldsProviderInterface;
use Mtf\Configuration\Reader;
use Magento\ObjectManager;

/**
 * Class Fixture
 *
 * Fixture files generator
 *
 * @package Mtf\Util\Generate
 * @internal
 */
class Fixture extends AbstractGenerate
{
    /**
     * @var Reader
     */
    protected $configReader;

    /**
     * @var FieldsProviderInterface
     */
    protected $fieldsProvider;

    /**
     * @constructor
     * @param ObjectManager $objectManager
     * @param Reader $configReader
     * @param FieldsProviderInterface $fieldsProvider
     */
    public function __construct(
        ObjectManager $objectManager,
        Reader $configReader,
        FieldsProviderInterface $fieldsProvider
    ) {
        parent::__construct($objectManager);
        $this->configReader = $configReader;
        $this->fieldsProvider = $fieldsProvider;
    }

    /**
     * Launch Fixture generators
     */
    public function launch()
    {
        $this->generateXml();
        $this->generateClasses();
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Generate Fixtures XML
     */
    protected function generateXml()
    {
        $this->cnt = 0;
        $configuration = $this->configReader->read('fixture');
        foreach ($configuration as $name => $item) {
            $this->generateFixtureXml($name, $item);
        }
        \Mtf\Util\Generate\GenerateResult::addResult('Fixture XML Files', $this->cnt);
    }

    /**
     * Generate fixtures XML definition files
     *
     * @param string $name
     * @param array $item
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
        $content .= '/**' . "\n";
        $content .= ' * {license_notice}' . "\n";
        $content .= ' *' . "\n";
        $content .= ' * @copyright   {copyright}' . "\n";
        $content .= ' * @license     {license_link}' . "\n";
        $content .= ' */' . "\n";
        $content .= '-->' . "\n";
        $content .= '<fixture class="' . $className . '">' . "\n";

        $fields = $this->fieldsProvider->getFields($item);
        if (!empty($item['fields']) && is_array($item['fields']) ){
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

        $content .= $this->toXml($item, '', '    ');

        $content .= "</fixture>\n";
        file_put_contents($folderName . '/' . $fileName, $content);
        $this->cnt++;
    }

    /**
     * Generate Fixtures Classes
     */
    protected function generateClasses()
    {
        $this->cnt = 0;
        $items = $this->collectFixturesXml();
        foreach ($items as $item) {
            $this->generateClass($item);
        }
        \Mtf\Util\Generate\GenerateResult::addResult('Fixture Classes', $this->cnt);
    }

    /**
     * Collect all fixtures .xml files
     *
     * @return array
     */
    protected function collectFixturesXml()
    {
        $items = [];
        $path = MTF_TESTS_PATH .'*/*';
        $modulesPages = glob($path);
        foreach ($modulesPages as $modulePath) {
            $modulePathArray = explode('/', $modulePath);
            $module = array_pop($modulePathArray);
            $namespace = array_pop($modulePathArray);
            if (!is_readable($modulePath . '/Test/Fixture')) {
                continue;
            }
            $dirIterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($modulePath . '/Test/Fixture', \FilesystemIterator::SKIP_DOTS)
            );
            foreach ($dirIterator as $fileInfo) {
                /** @var $fileInfo \SplFileInfo */
                $fileExt = $fileInfo->getExtension();
                if ($fileExt === 'xml') {
                    $items[] = [
                        'file_name' => $fileInfo->getBasename('.xml'),
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
     * Generate fixture classes from XML sources
     *
     * @param array $item
     */
    protected function generateClass(array $item)
    {
        $className = $item['file_name'];
        $modulePath = $item['module_path'];
        $folderPath = $item['folder_path'];
        $realPath = $item['real_path'];
        $namespace = $item['namespace'];
        $module = $item['module'];
        $contentXml = simplexml_load_file($realPath);
        $mTime = filemtime($realPath);
        $fields = [];
        $fieldsXml = $contentXml->xpath('fields');
        if ($fieldsXml) {
            foreach ($fieldsXml[0] as $fieldXml) {
                $name = $fieldXml->getName();
                $fields[$name] = (array) $fieldXml;
            }
        }
        $relativeFilePath = str_replace($modulePath . '/', '', $folderPath);
        $ns = $namespace . '\\' . $module . '\\' . str_replace('/', '\\', $relativeFilePath);
        $content = "<?php\n";
        $content .= "/**\n";
        $content .= " * {license_notice}\n";
        $content .= " *\n";
        $content .= " * @copyright   {copyright}\n";
        $content .= " * @license     {license_link}\n";
        $content .= " */\n\n";
        $content .= "namespace {$ns};\n\n";
        $content .= "use Mtf\\Fixture\\InjectableFixture;\n\n";
        $content .= "/**\n";
        $content .= " * Class {$className}\n";
        $content .= " *\n";
        $content .= " * @package {$ns}\n";
        $content .= " */\n";
        $content .= "class {$className} extends InjectableFixture\n";
        $content .= "{\n";

        if (isset($contentXml->repository_class)) {
            $content .= "    /**\n";
            $content .= "     * @var string\n";
            $content .= "     */\n";
            $content .= "    protected \$repositoryClass = '{$contentXml->repository_class}';\n\n";
        }
        if (isset($contentXml->handler_interface)) {
            $content .= "    /**\n";
            $content .= "     * @var string\n";
            $content .= "     */\n";
            $content .= "    protected \$handlerInterface = '{$contentXml->handler_interface}';\n\n";
        }

        if (isset($contentXml->data_config)) {
            $dataConfig = $this->configReader->convert($contentXml->data_config[0]);
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
            $content .= "\n" . '    public function get'.$this->toCamelCase($name) . '()' . "\n";
            $content .= "    {\n";
            $content .= "        return \$this->getData('".$name."');" . "\n";
            $content .= "    }\n";
        }
        $content .= "}\n";
        $newFilename = $className . '.php';
        if (file_exists($folderPath . '/' . $newFilename)) {
            $mmTime = filemtime($folderPath . '/' . $newFilename);
            if ($mTime > $mmTime) {
                return; // in order to not overwrite old fixtures
                unlink($folderPath . '/' . $newFilename);
            } else {
                return;
            }
        }
        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0777, true);
        }
        file_put_contents($folderPath . '/' . $newFilename, $content);
        touch($folderPath . '/' . $newFilename, $mTime);
        $this->cnt++;
    }

    /**
     * @param array $data
     * @param string $tag
     * @param string $tab
     * @return string
     */
    protected function toXml(array $data, $tag = '', $tab)
    {
        $xml = '';
        foreach ($data as $fieldName => $fieldValue) {
            if (is_array($fieldValue)) {
                $fieldValue = $this->toXml($fieldValue, '', $tab . '    ');
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

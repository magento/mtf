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
use Magento\Mtf\Config\DataInterface;
use Magento\Mtf\Util\XmlConverter;
use Magento\Mtf\ObjectManagerInterface;

/**
 * Class Fixture
 * Fixture files generator
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
     * @var FieldsProviderInterface
     */
    protected $fieldsProvider;

    /**
     * @var XmlConverter
     */
    protected $xmlConverter;

    /**
     * @constructor
     * @param ObjectManagerInterface $objectManager
     * @param DataInterface $configData
     * @param FieldsProviderInterface $fieldsProvider
     * @param XmlConverter $xmlConverter
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        DataInterface $configData,
        FieldsProviderInterface $fieldsProvider,
        XmlConverter $xmlConverter
    ) {
        parent::__construct($objectManager);
        $this->configData = $configData;
        $this->fieldsProvider = $fieldsProvider;
        $this->xmlConverter = $xmlConverter;
    }

    /**
     * Launch Fixture generators
     *
     * @return void
     */
    public function launch()
    {
        $this->generateXml();
        $this->generateClasses();
    }

    /**
     * Generate Fixtures XML
     *
     * @return void
     */
    protected function generateXml()
    {
        $this->cnt = 0;
        foreach ($this->configData->get() as $name => $item) {
            $this->generateFixtureXml($name, $item);
        }
        \Magento\Mtf\Util\Generate\GenerateResult::addResult('Fixture XML Files', $this->cnt);
    }

    /**
     * Generate fixtures XML definition files
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
     * Generate Fixtures Classes
     *
     * @return void
     */
    protected function generateClasses()
    {
        $this->cnt = 0;
        $items = $this->collectFixturesXml();
        foreach ($items as $item) {
            $this->generateClass($item);
        }
        \Magento\Mtf\Util\Generate\GenerateResult::addResult('Fixture Classes', $this->cnt);
    }

    /**
     * Collect all fixtures .xml files
     *
     * @return array
     */
    protected function collectFixturesXml()
    {
        $items = [];
        $path = MTF_TESTS_PATH . '*/*';
        $modulesPages = glob($path);
        foreach ($modulesPages as $modulePath) {
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
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
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
                $fields[$name] = (array)$fieldXml;
            }
        }
        $relativeFilePath = str_replace($modulePath . '/', '', $folderPath);
        $ns = $namespace . '\\' . $module . '\\' . str_replace('/', '\\', $relativeFilePath);
        $content = "<?php\n";
        $content .= $this->getFilePhpDoc();
        $content .= "namespace {$ns};\n\n";
        $content .= "use Magento\Mtf\\Fixture\\InjectableFixture;\n\n";
        $content .= "/**\n";
        $content .= " * Class {$className}\n";
        $content .= " */\n";
        $content .= "class {$className} extends InjectableFixture\n";
        $content .= "{\n";

        if (isset($contentXml->{'repository_class'})) {
            $content .= "    /**\n";
            $content .= "     * @var string\n";
            $content .= "     */\n";
            $content .= "    protected \$repositoryClass = '{$contentXml->{'repository_class'}}';\n\n";
        }
        if (isset($contentXml->{'handler_interface'})) {
            $content .= "    /**\n";
            $content .= "     * @var string\n";
            $content .= "     */\n";
            $content .= "    protected \$handlerInterface = '{$contentXml->{'handler_interface'}}';\n\n";
        }

        if (isset($contentXml->{'data_config'})) {
            $dataConfig = $this->xmlConverter->convert($contentXml->{'data_config'}[0]);
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

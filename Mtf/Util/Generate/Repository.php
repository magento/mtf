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

use Mtf\Util\Generate\Repository\CollectionProviderInterface;
use Mtf\Configuration\Reader;
use Mtf\Repository\Reader\Reader as RepositoryReader;
use Magento\Framework\ObjectManagerInterface;

/**
 * Repository files generator.
 */
class Repository extends AbstractGenerate
{
    /**
     * @var Reader
     */
    protected $configReader;

    /**
     * @var CollectionProviderInterface
     */
    protected $collectionProvider;

    /**
     * Reader for all repository xml files.
     *
     * @var RepositoryReader
     */
    protected $repositoryReader;

    /**
     * @constructor
     * @param ObjectManagerInterface $objectManager
     * @param Reader $configReader
     * @param CollectionProviderInterface $collectionProvider
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Reader $configReader,
        CollectionProviderInterface $collectionProvider,
        RepositoryReader $repositoryReader
    ) {
        parent::__construct($objectManager);
        $this->configReader = $configReader;
        $this->collectionProvider = $collectionProvider;
        $this->repositoryReader = $repositoryReader;
    }

    /**
     * Launch Repository generators.
     *
     * @return void
     */
    public function launch()
    {
        $this->generateXml();
        $this->generateClasses();
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Generate Repositories XML.
     *
     * @return void
     */
    protected function generateXml()
    {
        $this->cnt = 0;

        $configuration = $this->configReader->read('fixture');
        foreach ($configuration as $name => $item) {
            $this->generateRepositoryXml($name, $item);
        }
        \Mtf\Util\Generate\GenerateResult::addResult('Repository XML Files', $this->cnt);
    }

    /**
     * Generate repository XML definition files.
     *
     * @param string $name
     * @param array $item
     * @return void
     */
    protected function generateRepositoryXml($name, array $item)
    {
        $classShortName = ucfirst($name);
        $fileName = $classShortName . '.xml';
        $moduleName = $item['module'];
        $path = str_replace('_', '/', $moduleName) . '/Test/Repository';
        $className = str_replace('_', '\\', $moduleName) . '\\Test\\Repository\\' . $classShortName;
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
        $content .= "<repository xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" ";
        $content .= "xsi:noNamespaceSchemaLocation=\"../../../../../../Mtf/Repository/etc/repository.xsd\">\n";
        $content .= "    <storage class=\"{$className}\">\n";
        $collection = $this->collectionProvider->getCollection($item);
        foreach ($collection as $record) {
            $record['mtf_dataset_name'] = $this->buildDataSetName($item, $record);
            $content .= $this->toXml($record);
        }
        if (count($collection) === 0) {
            $content .= $this->getDummyXml();
        }
        $content .= "    </storage>\n";
        $content .= "</repository>\n";

        $fh = fopen($folderName . '/' . $fileName, 'w+');
        fwrite($fh, $content);
        fclose($fh);
        $this->cnt++;
    }

    /**
     * Build data set name based on identifier
     *
     * @param array $item
     * @param array $record
     * @return string
     */
    protected function buildDataSetName(array $item, array $record)
    {
        if (!empty($item['identifier'])) {
            $name = $record[$item['identifier']];
        } else {
            $name = 'dataset_' . $record['id'];
        }

        return $name;
    }

    /**
     * Convert array to XML format.
     *
     * @param array $data
     * @param string $tag
     * @return string
     */
    protected function toXml(array $data, $tag = 'dataset')
    {
        $xml = '';
        foreach ($data as $fieldName => $fieldValue) {
            if (is_array($fieldValue)) {
                $fieldValue = serialize($fieldValue);
            }

            if (!is_numeric($fieldValue)) {
                $fieldValue = "<![CDATA[$fieldValue]]>";
            }
            $xml .= "            ";
            $xml .= "<field name=\"{$fieldName}\">{$fieldValue}</field>\n";
        }
        $xml = "        <{$tag} name=\"{$data['mtf_dataset_name']}\">\n{$xml}        </{$tag}>\n";
        return $xml;
    }

    /**
     * Returns dummy dataSet xml.
     *
     * @return string
     */
    protected function getDummyXml()
    {
        $content = "        <dataset name=\"dummy_dataset\">\n";
        $content .= "            <field name=\"dummy_simple_field\">test_value</field>\n";
        $content .= "            <field name=\"dummy_array_field\">\n";
        $content .= "                <item name=\"dummy_array_0\">test_value_0</item>\n";
        $content .= "                <item name=\"dummy_array_1\">test_value_1</item>\n";
        $content .= "            </field>\n";
        $content .= "        </dataset>\n";

        return $content;
    }

    /**
     * Generate Repositories Classes.
     *
     * @return void
     */
    protected function generateClasses()
    {
        $this->cnt = 0;
        $items = $this->repositoryReader->read();
        foreach ($items as $class => $item) {
            $this->generateClass($item, $class);
        }
        \Mtf\Util\Generate\GenerateResult::addResult('Repository Classes', $this->cnt);
    }

    /**
     * Generate repository classes from XML sources.
     *
     * @param array $items
     * @return void
     */
    protected function generateClass(array $items, $class)
    {
        $class = explode("\\", $class);

        $className = end($class);
        $modulePath = MTF_BP . '/generated/' . implode(DIRECTORY_SEPARATOR,
                array_diff($class, [$className, 'Test', 'Repository']));
        $folderPath = $modulePath . '/Test/Repository';
        $realPath = realpath($folderPath);
        $namespace = $class[0];
        $module = $class[1];

        $mTime = filemtime($realPath);

        $relativeFilePath = str_replace($modulePath . '/', '', $folderPath);
        $ns = $namespace . '\\' . $module . '\\' . str_replace('/', '\\', $relativeFilePath);
        $content = "<?php\n";
        $content .= $this->getFilePhpDoc();
        $content .= "namespace {$ns};\n\n";
        $content .= "use Mtf\\Repository\\AbstractRepository;\n\n";
        $content .= "/**\n";
        $content .= " * Class {$className}\n";
        $content .= " */\n";
        $content .= "class {$className} extends AbstractRepository\n";
        $content .= "{\n";

        $content .= '    public function __construct(array $defaultConfig = [], array $defaultData = [])' . "\n";
        $content .= "    {\n";
        foreach ($items as $name => $item) {
            $content .= "        \$this->_data['{$name}'] = ";
            $content .= $this->generateArray('', $item, '        ');
            $content .= "        ];\n\n";
        }
        $content .= "    }\n";

        $content .= "}\n";

        $newFilename = $className . '.php';

        if (file_exists($folderPath . '/' . $newFilename)) {
            $mmTime = filemtime($folderPath . '/' . $newFilename);
            if ($mTime > $mmTime) {
                return; // in order to not overwrite old repositories
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
        $content = $arrayKey == '' ? "[\n" : $indent . "'{$arrayKey}' => [\n";
        foreach ($params as $key => $value) {
            $content .= is_array($value)
                ? $this->generateArray($key, $value, $indent . '    ', true)
                : ($indent . "    '{$key}' => '{$value}',\n");
        }
        $content .= !$flag ? '' : $indent . "],\n";

        return $content;
    }
}

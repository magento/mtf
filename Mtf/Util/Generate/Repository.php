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
use Mtf\ObjectManagerInterface;

/**
 * Class Repository
 * Repository files generator
 *
 * @internal
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
     * @constructor
     * @param ObjectManagerInterface $objectManager
     * @param Reader $configReader
     * @param CollectionProviderInterface $collectionProvider
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Reader $configReader,
        CollectionProviderInterface $collectionProvider
    ) {
        parent::__construct($objectManager);
        $this->configReader = $configReader;
        $this->collectionProvider = $collectionProvider;
    }

    /**
     * Launch Repository generators
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
     * Generate Repositories XML
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
     * Generate repository XML definition files
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

        $content .= '<repository class="' . $className . '" >' . "\n";
        $collection = $this->collectionProvider->getCollection($item);
        foreach ($collection as $record) {
            $record['mtf_dataset_name'] = $this->buildDataSetName($item, $record);
            $content .= $this->toXml($record);
        }
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
     * Convert array to XML format
     *
     * @param array $data
     * @param string $tag
     * @return string
     */
    protected function toXml(array $data, $tag = 'item')
    {
        $xml = '';
        foreach ($data as $fieldName => $fieldValue) {
            if (is_array($fieldValue)) {
                $fieldValue = serialize($fieldValue);
            }

            if (!is_numeric($fieldValue)) {
                $fieldValue = "<![CDATA[$fieldValue]]>";
            }

            $xml .= "<{$fieldName}>{$fieldValue}</{$fieldName}>\n";
        }
        $xml = "<{$tag}>\n{$xml}</{$tag}>\n";
        return $xml;
    }

    /**
     * Generate Repositories Classes
     *
     * @return void
     */
    protected function generateClasses()
    {
        $this->cnt = 0;

        $items = $this->collectRepositoriesXml();

        foreach ($items as $item) {
            $this->generateClass($item);
        }
        \Mtf\Util\Generate\GenerateResult::addResult('Repository Classes', $this->cnt);
    }

    /**
     * Collect all repository .xml files
     *
     * @return array
     */
    protected function collectRepositoriesXml()
    {
        $items = [];

        $path = MTF_TESTS_PATH . '*/*';

        $modulesPages = glob($path);

        foreach ($modulesPages as $modulePath) {
            $modulePathArray = explode('/', $modulePath);
            $module = array_pop($modulePathArray);
            $namespace = array_pop($modulePathArray);

            if (!is_readable($modulePath . '/Test/Repository')) {
                continue;
            }

            $dirIterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($modulePath . '/Test/Repository', \FilesystemIterator::SKIP_DOTS)
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
     * Generate repository classes from XML sources
     *
     * @param array $item
     * @return void
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

        $items = [];
        $itemsXml = $contentXml->xpath('item');
        foreach ($itemsXml as $itemXml) {
            $items[] = (array)$itemXml;
        }

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
        foreach ($items as $item) {
            $name = $item['mtf_dataset_name'];
            $content .= "        \$this->_data['{$name}'] = [\n";
            foreach ($item as $key => $value) {
                $content .= "            '{$key}' => '" . addslashes($value) . "',\n";
            }
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
}

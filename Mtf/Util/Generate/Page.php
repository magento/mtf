<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Util\Generate;

use Magento\ObjectManager;
use Mtf\Configuration\Reader;
use Mtf\Util\Generate\Fixture\FieldsProviderInterface;

/**
 * Class Page
 *
 * Page files generator
 *
 * @package Mtf\Util\Generate
 * @internal
 */
class Page extends AbstractGenerate
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
     */
    public function __construct(
        ObjectManager $objectManager,
        Reader $configReader
    ) {
        parent::__construct($objectManager);

        $this->configReader = $configReader;
    }

    /**
     * Launch Page generators
     */
    public function launch()
    {
        $this->generateXml();
        $this->generateClasses();
    }

    /**
     * Generate Pages XML
     */
    public function generateXml()
    {
        $this->cnt = 0;

        $configuration = $this->configReader->read('page');
        foreach ($configuration as $item) {
            $this->generatePageXml($item);
        }
        \Mtf\Util\Generate\GenerateResult::addResult('Page XML Files', $this->cnt);
    }

    /**
     * Generate page XML configuration file from source definition
     *
     * @param array $item
     */
    private function generatePageXml(array $item)
    {
        $className = $item['class'];

        $classNameArray = explode('\\', $className);
        $fileName = array_pop($classNameArray);
        $path = implode('/', $classNameArray);

        $content = '<?xml version="1.0" ?>' . "\n";
        $content .= '<!--' . "\n";
        $content .= '/**' . "\n";
        $content .= ' * {license_notice}' . "\n";
        $content .= ' *' . "\n";
        $content .= ' * @copyright   {copyright}' . "\n";
        $content .= ' * @license     {license_link}' . "\n";
        $content .= ' */' . "\n";
        $content .= '-->' . "\n";

        $layoutMCA = $item['mca'];
        $content .= '<page mca="' . $layoutMCA . '" >' . "\n";

        $blocks = $this->collectPageBlocks($item);
        foreach ($blocks as $block) {
            $content .= '    <block>' . "\n";
            foreach ($block as $key => $value) {
                $content .= "        <{$key}>{$value}</$key>\n";
            }
            $content .= '    </block>' . "\n";
        }

        $content .= "</page>\n";

        $newFolderName = MTF_TESTS_PATH . $path;
        $newFilename = $fileName . '.xml';

        if (file_exists($newFolderName . '/' . $newFilename)) {
            //unlink($newFolderName . '/' . $newFilename);
            return;
        }

        if (!is_dir($newFolderName)) {
            mkdir($newFolderName, 0777, true);
        }

        file_put_contents($newFolderName . '/' . $newFilename, $content);

        $this->cnt++;
    }

    /**
     * Collect all blocks for page
     *
     * @param array $mca
     * @return array
     */
    private function collectPageBlocks(array $mca)
    {
        return [
            [
                'name' => 'testBlock',
                'class' => 'Magento\Mtf\Test\Block\TestBlock',
                'locator' => 'body',
                'strategy' => 'tag name'
            ]
        ];
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Generate Pages Classes
     */
    public function generateClasses()
    {
        $this->cnt = 0;

        $items = $this->collectPagesXml();

        foreach ($items as $item) {
            $this->generatePageClass($item);
        }
        \Mtf\Util\Generate\GenerateResult::addResult('Page Classes', $this->cnt);
    }

    /**
     * @return array
     */
    private function collectPagesXml()
    {
        $items = [];

        $modules = glob(MTF_TESTS_PATH . '*/*');
        foreach ($modules as $modulePath) {
            $modulePathArray = explode('/', $modulePath);
            $module = array_pop($modulePathArray);
            $namespace = array_pop($modulePathArray);

            if (!is_readable($modulePath . '/Test/Page')) {
                continue;
            }

            $dirIterator = new \RegexIterator(
                new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($modulePath . '/Test/Page', \FilesystemIterator::SKIP_DOTS)
                ), '/.xml$/i'
            );

            foreach ($dirIterator as $fileInfo) {
                /** @var $fileInfo \SplFileInfo */
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

        return $items;
    }

    /**
     * Generate page classes from XML sources
     *
     * @param array $item
     */
    private function generatePageClass(array $item)
    {
        $className = $item['file_name'];
        $modulePath = $item['module_path'];
        $folderPath = $item['folder_path'];
        $realPath = $item['real_path'];
        $namespace = $item['namespace'];
        $module = $item['module'];
        $area = strpos($folderPath, 'Adminhtml') === false ? 'FrontendPage' : 'BackendPage';

        $contentXml = simplexml_load_file($realPath);
        $mTime = filemtime($realPath);

        $mca = $contentXml['mca'];

        $blocks = [];
        $blocksXml = $contentXml->xpath('block');
        foreach ($blocksXml as $blockXml) {
            $blocks[] = (array) $blockXml;
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
        $content .= "namespace {$ns}; \n\n";
        $content .= "use Mtf\\Page\\{$area}; \n\n";
        $content .= "/**\n";
        $content .= " * Class {$className}\n";
        $content .= " *\n";
        $content .= " * @package {$ns}\n";
        $content .= " */\n";
        $content .= "class {$className} extends {$area}\n";
        $content .= "{\n";
        $content .= "    const MCA = '{$mca}';\n\n";

        $content .= "    protected \$_blocks = [\n";
        foreach ($blocks as $block) {
            $content .= "        '{$block['name']}' => [\n";
            foreach ($block as $key => $value) {
                $content .= "            '{$key}' => '{$value}',\n";
            }
            $content .= "        ],\n";
        }
        $content .= "    ];\n";

        foreach ($blocks as $block) {
            $content .= "\n    /**\n";
            $content .= "     * @return \\{$block['class']}\n";
            $content .= "     */\n";
            $content .= '    public function get'.ucfirst($block['name']) . '()' . "\n";
            $content .= "    {\n";
            $content .= "        return \$this->getBlockInstance('{$block['name']}');\n";
            $content .= "    }\n";
        }

        $content .= "}\n";

        $newFilename = $className . '.php';

        if (file_exists($folderPath . '/' . $newFilename)) {
            $mmTime = filemtime($folderPath . '/' . $newFilename);
            if ($mTime > $mmTime) {
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
